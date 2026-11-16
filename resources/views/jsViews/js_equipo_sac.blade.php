<script>
/* Equipo SAC.
   En vivo, Bitácora y Resumen leen los estados de tbl_estado_log (EstadoSacController@equipo).
   Pedidos lee tbl_order: quién cambió el estado (updated_by), cuándo llegó y cuándo se procesó. */
(function () {
  'use strict';

  var NOMBRE = { on: 'En línea', afk: 'AFK', lunch: 'Descanso', off: 'Desconectado' };
  var ORIGEN = { manual: 'Manual', sin_senal: 'Sin señal', logout: 'Cierre de sesión' };
  var ORDEN_ESTADO = { on: 0, afk: 1, lunch: 2, off: 3 };
  var MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
  var DIA0 = 8 * 60, DIA1 = 18 * 60;   // eje de la línea de tiempo: 08:00 a 18:00
  var URL_ESTADOS = @json(route('equipoEstados'));
  var URL_EXCEL = @json(route('equipoResumenExcel'));
  var URL_PEDIDOS = @json(route('equipoPedidos'));
  var URL_APPS = @json(route('equipoAplicaciones'));
  var URL_APP_CAT = @json(route('equipoAppCategoria'));

  // ---------- Equipo: SAC y SAC Supervisor activos ----------
  var SACS = (window.SAC_EQ_SACS || []).slice();
  var POR_ID = {};
  SACS.forEach(function (s) {
    var p = (s.nombre || '?').split(/\s+/).filter(Boolean);
    s.ini = ((p[0] || '?').charAt(0) + (p[1] || '').charAt(0)).toUpperCase();
    POR_ID[s.id] = s;
  });

  // ---------- Utilidades ----------
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function pad(n) { return (n < 10 ? '0' : '') + n; }
  function hhmm(min) { min = Math.round(min); return pad(Math.floor(min / 60)) + ':' + pad(min % 60); }
  function dur(min) {
    min = Math.round(min);
    if (min < 1) return '<1 min';
    if (min < 60) return min + ' min';
    return Math.floor(min / 60) + ' h ' + pad(min % 60) + ' min';
  }
  function horas(min) { return (min / 60).toLocaleString('es-NI', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' h'; }
  function pct(a, b) { return b > 0 ? Math.round(a * 100 / b) : null; }
  function money(v) { return 'C$ ' + v.toLocaleString('es-NI', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
  function moneyK(v) { return v >= 1e6 ? 'C$ ' + (v / 1e6).toLocaleString('es-NI', { maximumFractionDigits: 2 }) + ' M' : money(v); }
  function clave(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
  function fechaCorta(d) { return pad(d.getDate()) + ' ' + MESES[d.getMonth()]; }
  function soloDia(d) { var x = new Date(d); x.setHours(0, 0, 0, 0); return x; }
  function mismoDia(a, b) { return clave(a) === clave(b); }
  // "AAAA-MM-DD HH:MM:SS" (hora de Managua, la del servidor) a Date local
  function fechaSrv(s) {
    var m = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2}):(\d{2})/.exec(s || '');
    return m ? new Date(+m[1], +m[2] - 1, +m[3], +m[4], +m[5], +m[6]) : null;
  }

  // ---------- Hora de referencia: la del servidor (se actualiza en cada consulta) ----------
  var desfase = 0;                                         // reloj del servidor - reloj local
  function ahoraDate() { return new Date(Date.now() + desfase); }
  function ahoraMin() { var d = ahoraDate(); return d.getHours() * 60 + d.getMinutes() + d.getSeconds() / 60; }
  var HOY = soloDia(ahoraDate());

  // =====================================================================
  // Datos reales de estados
  // =====================================================================
  // DATOS.dias['<id>|AAAA-MM-DD'] = [{ e, a, b, abierto, cierre, previo }] en minutos del día,
  // recortados al día. abierto = sigue en curso; cierre = cómo terminó (si terminó ese día);
  // previo = el tramo empezó el día anterior.
  var DATOS = { clave: '', dias: {}, cargado: false };
  var req = 0;

  function indexar(r) {
    var dias = {}, ahora = fechaSrv(r.ahora) || ahoraDate();
    (r.tramos || []).forEach(function (t) {
      var a = fechaSrv(t.inicio), b = t.fin ? fechaSrv(t.fin) : ahora, abierto = !t.fin;
      if (!a || !b || b <= a) return;
      var d = soloDia(a);
      while (d < b) {
        var d1 = new Date(d); d1.setDate(d1.getDate() + 1);
        var ia = Math.max(a, d), ib = Math.min(b, d1);
        if (ib > ia) {
          var k = t.user_id + '|' + clave(d);
          (dias[k] = dias[k] || []).push({
            e: t.estado,
            a: (ia - d) / 60000, b: (ib - d) / 60000,
            abierto: abierto && b <= d1,
            cierre: (!abierto && b <= d1) ? t.cierre : null,
            previo: a < d
          });
        }
        d = d1;
      }
    });
    Object.keys(dias).forEach(function (k) { dias[k].sort(function (x, y) { return x.a - y.a; }); });
    return dias;
  }

  // Trae los estados del rango (o de hoy para En vivo) y luego pinta
  function cargar(desde, hasta, listo) {
    var k = clave(desde) + '|' + clave(hasta), mio = ++req;
    estadoCarga(true);
    $.ajax({ url: URL_ESTADOS, data: { desde: clave(desde), hasta: clave(hasta) }, cache: false })
      .done(function (r) {
        if (mio !== req) return;
        var srv = fechaSrv(r.ahora);
        if (srv) { desfase = srv - Date.now(); HOY = soloDia(srv); }
        DATOS = { clave: k, dias: indexar(r), cargado: true };
        estadoCarga(false);
        listo();
      })
      .fail(function (xhr) {
        if (mio !== req) return;
        estadoCarga(false);
        if (xhr && xhr.status === 401) return; // sesión cerrada: el shell lleva al login
        var msg = (xhr && xhr.responseJSON && xhr.responseJSON.mensaje) || 'No se pudieron cargar los estados del equipo.';
        errorCarga(msg);
      });
  }
  function estadoCarga(on) { document.querySelector('.sac-eq').classList.toggle('is-loading', on); }
  function errorCarga(msg) {
    var html = '<p class="sac-empty">' + esc(msg) + ' <button type="button" class="btn btn-link" data-eq-reintentar>Reintentar</button></p>';
    if (F.tab === 'vivo') document.getElementById('sacEqGrid').innerHTML = html;
    else if (F.tab === 'resumen') { document.getElementById('sacEqSemHead').innerHTML = ''; document.getElementById('sacEqResBody').innerHTML = '<tr><td colspan="9">' + html + '</td></tr>'; }
    else if (F.tab === 'bitacora') { filasBit = []; pintarTablaBit([]); $('#sacEqTblBit').closest('.sac-eq-pane').find('.sac-eq-err').remove(); $('#sacEqTblBit').closest('.sac-eq-pane').prepend('<div class="sac-eq-err">' + html + '</div>'); }
  }
  document.addEventListener('click', function (e) { if (e.target.closest && e.target.closest('[data-eq-reintentar]')) refrescar(); });

  function tramosDe(s, d) { return DATOS.dias[s.id + '|' + clave(d)] || []; }

  function totalesDia(tr) {
    var t = { on: 0, afk: 0, lunch: 0 };
    tr.forEach(function (x) { t[x.e] += x.b - x.a; });
    return t;
  }

  // Cambios de estado de un día: inicio de cada tramo y paso a Desconectado cuando no sigue otro
  function cambiosDia(s, d) {
    var tr = tramosDe(s, d), out = [];
    tr.forEach(function (x, i) {
      var ant = tr[i - 1], sig = tr[i + 1];
      var pegadoAnt = ant && Math.abs(ant.b - x.a) < 0.02;
      if (!x.previo) out.push({ d: d, min: x.a, s: s, de: pegadoAnt ? ant.e : 'off', a: x.e, dur: x.b - x.a, abierto: x.abierto, origen: 'manual' });
      var pegadoSig = sig && Math.abs(sig.a - x.b) < 0.02;
      if (x.cierre && !pegadoSig) out.push({ d: d, min: x.b, s: s, de: x.e, a: 'off', dur: null, abierto: false, origen: x.cierre });
    });
    return out;
  }

  // ---------- Estado de filtros ----------
  // Resumen: su propio rango (por defecto la semana actual, lunes a domingo)
  var F = { tab: 'vivo', desde: HOY, hasta: HOY, sac: '', resDesde: null, resHasta: null };

  function diasDelRango() {
    var out = [], d = new Date(F.desde);
    while (d <= F.hasta && out.length < 93) { out.push(new Date(d)); d.setDate(d.getDate() + 1); }
    return out;
  }
  function sacsFiltrados() { return F.sac ? SACS.filter(function (s) { return String(s.id) === F.sac; }) : SACS; }

  // ---------- Tooltip compartido ----------
  var tip = document.getElementById('sacEqTip');
  document.addEventListener('mousemove', function (e) {
    var el = e.target.closest ? e.target.closest('[data-tip]') : null;
    if (!el) { tip.hidden = true; return; }
    tip.innerHTML = el.getAttribute('data-tip');
    tip.hidden = false;
    var w = tip.offsetWidth, h = tip.offsetHeight, x = e.clientX + 14, y = e.clientY + 16;
    if (x + w > window.innerWidth - 8) x = e.clientX - w - 14;
    if (y + h > window.innerHeight - 8) y = e.clientY - h - 12;
    tip.style.left = x + 'px'; tip.style.top = y + 'px';
  });
  document.addEventListener('scroll', function () { tip.hidden = true; }, true);

  function tipTramo(sac, x) {
    return '<strong>' + esc(NOMBRE[x.e]) + '</strong><span>' + esc(sac.nombre) + '</span><span>' + hhmm(x.a) + ' – ' + (x.abierto ? 'ahora' : hhmm(x.b)) + ' · ' + dur(x.b - x.a) + '</span>';
  }
  function kpi(label, valor, sub, extra) {
    return '<div class="sac-kpi"><span class="sac-kl">' + label + '</span><p class="sac-kv">' + valor + '</p>' + (extra || '') + (sub ? '<p class="sac-muted">' + sub + '</p>' : '') + '</div>';
  }
  function tagEstado(e) { return '<span class="sac-eq-st is-' + e + '"><i class="sac-est-dot" aria-hidden="true"></i>' + NOMBRE[e] + '</span>'; }
  function avatar(s, e) { return '<span class="sac-avatar">' + esc(s.ini) + (e ? '<i class="sac-eq-avdot is-' + e + '" aria-hidden="true"></i>' : '') + '</span>'; }
  function nombreSac(s) { return '<div class="d-flex align-items-center gap-2">' + avatar(s) + '<div class="lh-sm"><span class="sac-cli-name d-block">' + esc(s.nombre) + '</span><span class="sac-cli-sub">' + esc(s.username) + '</span></div></div>'; }
  var ICO_ALERTA = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4M12 17h.01"></path></svg>';

  // =====================================================================
  // 1) EN VIVO
  // =====================================================================
  function tarjetasVivo() {
    var now = ahoraMin();
    return SACS.map(function (s) {
      var tr = tramosDe(s, HOY), ult = tr.length ? tr[tr.length - 1] : null;
      var act = ult && ult.abierto ? ult : null;
      return { s: s, e: act ? act.e : 'off', act: act, ult: ult, tr: tr, t: totalesDia(tr), now: now };
    }).sort(function (a, b) { return ORDEN_ESTADO[a.e] - ORDEN_ESTADO[b.e] || a.s.nombre.localeCompare(b.s.nombre); });
  }

  function pintarVivo() {
    var tarjetas = tarjetasVivo(), now = ahoraMin();
    var ticks = '';
    for (var h = 8; h <= 18; h += 2) ticks += '<span style="left:' + ((h * 60 - DIA0) * 100 / (DIA1 - DIA0)) + '%">' + h + '</span>';
    var span = DIA1 - DIA0;

    document.getElementById('sacEqGrid').innerHTML = tarjetas.length ? tarjetas.map(function (c) {
      var seg = c.tr.map(function (x) {
        var a = Math.max(x.a, DIA0), b = Math.min(x.b, DIA1);
        if (b <= a) return '';
        return '<i class="is-' + x.e + '" style="left:' + ((a - DIA0) * 100 / span).toFixed(2) + '%;width:max(6px, calc(' + ((b - a) * 100 / span).toFixed(2) + '% - 2px))" data-tip="' + esc(tipTramo(c.s, x)) + '"></i>';
      }).join('');
      var ahoraPos = ((Math.min(Math.max(now, DIA0), DIA1) - DIA0) * 100 / span).toFixed(2);
      var base = c.t.on + c.t.afk + c.t.lunch, ef = pct(c.t.on, base);
      var desde;
      if (c.act) desde = 'desde ' + hhmm(c.act.a) + ' · ' + dur(now - c.act.a);
      else if (c.ult) desde = 'Desconectado desde ' + hhmm(c.ult.b) + (c.ult.cierre === 'sin_senal' ? ' · sin señal' : '');
      else desde = 'Sin marcar hoy';
      // Solo un aviso visual para el supervisor; no cambia el estado
      var alerta = c.act && ((c.e === 'afk' && now - c.act.a >= 15) || (c.e === 'lunch' && now - c.act.a >= 60));
      return '<article class="sac-eq-card is-' + c.e + (alerta ? ' is-alert' : '') + '">' +
        '<header>' + avatar(c.s, c.e) +
          '<div class="sac-eq-who"><span class="sac-cli-name">' + esc(c.s.nombre) + '</span><span class="sac-cli-sub">' + esc(c.s.username) + '</span></div>' +
          tagEstado(c.e) + '</header>' +
        '<p class="sac-eq-since">' + (alerta ? ICO_ALERTA : '') + desde + '</p>' +
        '<div class="sac-eq-tl" role="img" aria-label="Línea de tiempo de hoy de ' + esc(c.s.nombre) + '">' +
          '<div class="sac-eq-track">' + seg + '<b class="sac-eq-now" style="left:' + ahoraPos + '%"></b></div>' +
          '<div class="sac-eq-ticks">' + ticks + '</div></div>' +
        '<dl class="sac-eq-stats">' +
          '<div><dt>Efectivo</dt><dd>' + (ef == null ? '—' : ef + '%') + '</dd></div>' +
          '<div><dt>En línea</dt><dd>' + (c.t.on ? horas(c.t.on) : '—') + '</dd></div>' +
          '<div><dt>Pausas</dt><dd>' + (c.t.afk + c.t.lunch ? horas(c.t.afk + c.t.lunch) : '—') + '</dd></div>' +
        '</dl></article>';
    }).join('') : '<p class="sac-empty">No hay usuarios SAC activos.</p>';

    document.getElementById('sacEqLiveTxt').textContent = 'Hoy · actualizado ' + hhmm(now);
  }

  // =====================================================================
  // 2) BITÁCORA
  // =====================================================================
  var dtBit = null, filasBit = [];
  function datosBitacora() {
    var out = [];
    diasDelRango().forEach(function (d) { sacsFiltrados().forEach(function (s) { out = out.concat(cambiosDia(s, d)); }); });
    out.sort(function (a, b) { return b.d - a.d || b.min - a.min; });
    return out;
  }
  function tagOrigen(o) {
    return '<span class="sac-tag ' + (o === 'sin_senal' ? 'sac-tag-warn' : 'sac-tag-neutral') + '">' + (ORIGEN[o] || 'Manual') + '</span>';
  }
  function pintarTablaBit(filas) {
    if (dtBit) { dtBit.clear().rows.add(filas).draw(); return; }
    dtBit = $('#sacEqTblBit').DataTable({
      data: filas, deferRender: true, pageLength: 15, lengthMenu: [15, 30, 50, 100], order: [[0, 'desc'], [1, 'desc']],
      columnDefs: [{ targets: [4], className: 'text-end' }, { targets: [3, 5], orderable: false }],
      language: lenguaje('cambios de estado'),
      dom: '<"sac-dt-top"f l>rt<"sac-dt-foot"ip>'
    });
  }
  function pintarBitacora() {
    $('.sac-eq-err').remove();
    filasBit = datosBitacora();
    pintarTablaBit(filasBit.map(function (r) {
      return [
        '<span data-order="' + clave(r.d) + '">' + fechaCorta(r.d) + '</span>',
        '<span class="sac-num" data-order="' + pad(Math.floor(r.min / 60)) + pad(Math.floor(r.min % 60)) + '">' + hhmm(r.min) + '</span>',
        nombreSac(r.s),
        '<span class="sac-eq-chg">' + tagEstado(r.de) + '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-label="a"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>' + tagEstado(r.a) + '</span>',
        r.dur == null ? '<span class="sac-muted">—</span>' : '<span class="sac-num' + ((r.a === 'afk' && r.dur >= 15) ? ' sac-eq-warn' : '') + '">' + dur(r.dur) + (r.abierto ? ' <small class="sac-muted">en curso</small>' : '') + '</span>',
        tagOrigen(r.origen)
      ];
    }));
  }

  // =====================================================================
  // 3) RESUMEN SEMANAL: tiempo de cada SAC en cada estado, por día
  // =====================================================================
  var DIAS_HABILES = [1, 2, 3, 4, 5];   // 1 = lunes … 5 = viernes (sábado y domingo van rayados)
  var DIAS_ABR = ['DOM', 'LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB'];
  var DIAS_NOM = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

  function lunesDe(d) { var x = soloDia(d); x.setDate(x.getDate() - (x.getDay() + 6) % 7); return x; }
  function sumarDias(d, n) { var x = new Date(d); x.setDate(x.getDate() + n); return x; }
  function esHabil(d) { return DIAS_HABILES.indexOf(d.getDay()) !== -1; }
  // Duración en HH:MM:SS (las horas pueden pasar de 24 en el total de la semana)
  function durC(min) {
    var seg = Math.max(0, Math.round(min * 60));
    return pad(Math.floor(seg / 3600)) + ':' + pad(Math.floor(seg % 3600 / 60)) + ':' + pad(seg % 60);
  }
  var MAX_DIAS_RES = 31;   // columnas como máximo en el resumen
  function diasSemana() {
    var out = [], d = new Date(F.resDesde);
    while (d <= F.resHasta && out.length < MAX_DIAS_RES) { out.push(new Date(d)); d.setDate(d.getDate() + 1); }
    return out;
  }
  function textoSemana() {
    var a = F.resDesde, b = F.resHasta;
    var t = function (x) { return x.getDate() + ' ' + MESES[x.getMonth()]; };
    if (mismoDia(a, b)) return t(a) + ' ' + a.getFullYear();
    return (a.getMonth() === b.getMonth() && a.getFullYear() === b.getFullYear() ? a.getDate() : t(a)) + ' – ' + t(b) + ' ' + b.getFullYear();
  }

  // Tiempo de un SAC en un día
  function infoDia(s, d) {
    var tr = tramosDe(s, d), r = { d: d, hoy: mismoDia(d, HOY), habil: esHabil(d) };
    if (d > HOY) { r.tipo = 'futuro'; return r; }
    if (!tr.length) { r.tipo = 'vacio'; return r; }
    r.tipo = 'datos';
    r.t = totalesDia(tr);
    r.base = r.t.on + r.t.afk + r.t.lunch;
    r.ef = pct(r.t.on, r.base);
    r.enCurso = tr[tr.length - 1].abierto;
    return r;
  }

  // Tiempo por estado (se usa en cada día y en el total de la semana)
  function bloqueEstados(t, base, ef, enCurso, tip) {
    return '<div class="sac-eq-tb"' + (tip ? ' data-tip="' + esc(tip) + '"' : '') + '>' +
      ['on', 'afk', 'lunch'].map(function (k) {
        return '<div class="sac-eq-tb-r is-' + k + (t[k] >= 1 / 60 ? '' : ' is-cero') + '"><i class="sac-est-dot" aria-hidden="true"></i><span class="sac-eq-tb-l">' + NOMBRE[k] + '</span><span class="sac-eq-tb-v">' + durC(t[k]) + '</span></div>';
      }).join('') +
      '<div class="sac-eq-tb-ef">' + (ef == null ? '—' : ef + '% efectivo') + (enCurso ? ' · <span class="sac-eq-tb-cur">en curso</span>' : '') + '</div>' +
    '</div>';
  }

  function celdaDia(s, i) {
    var cls = 'sac-eq-dia' + (i.hoy ? ' is-hoy' : '') + (!i.habil ? ' is-fds' : '');
    if (i.tipo === 'futuro') return '<td class="' + cls + ' is-fut"></td>';
    if (i.tipo === 'vacio') return '<td class="' + cls + ' is-vacio">' + (i.habil ? '<span class="sac-muted">Sin registro</span>' : '') + '</td>';
    var tip = '<strong>' + esc(s.nombre) + '</strong><span>' + DIAS_NOM[i.d.getDay()] + ' ' + fechaCorta(i.d) + '</span>' +
      '<span>En línea ' + durC(i.t.on) + '</span><span>AFK ' + durC(i.t.afk) + '</span><span>Descanso ' + durC(i.t.lunch) + '</span>' +
      '<span>Total ' + durC(i.base) + ' · efectivo ' + (i.ef == null ? '—' : i.ef + '%') + '</span>';
    return '<td class="' + cls + '">' + bloqueEstados(i.t, i.base, i.ef, i.enCurso, tip) + '</td>';
  }

  function pintarResumen() {
    var dias = diasSemana();
    var variosMeses = dias.length && dias[0].getMonth() !== dias[dias.length - 1].getMonth();
    var leyenda = ['on', 'afk', 'lunch'].map(function (k) { return '<span class="is-' + k + '"><i class="sac-est-dot" aria-hidden="true"></i>' + NOMBRE[k] + '</span>'; }).join('');

    document.getElementById('sacEqSemHead').innerHTML = '<tr><th class="sac-eq-col-sac" scope="col"><span class="sac-eq-th">Colaborador</span>' +
      '<span class="sac-eq-th-ley">' + leyenda + '</span></th>' +
      dias.map(function (d) {
        return '<th scope="col" class="sac-eq-dia' + (mismoDia(d, HOY) ? ' is-hoy' : '') + (!esHabil(d) ? ' is-fds' : '') + '"><span class="sac-eq-dnum">' + d.getDate() + '</span><span class="sac-eq-dabr">' + DIAS_ABR[d.getDay()] + (variosMeses ? ' · ' + MESES[d.getMonth()].toUpperCase() : '') + '</span></th>';
      }).join('') + '</tr>';
    // El total del periodo solo va en el Excel (Exportar)

    var lista = sacsFiltrados().slice().sort(function (a, b) { return a.nombre.localeCompare(b.nombre); });
    document.getElementById('sacEqResBody').innerHTML = lista.length ? lista.map(function (s) {
      var celdas = dias.map(function (d) { return celdaDia(s, infoDia(s, d)); }).join('');
      return '<tr><th scope="row" class="sac-eq-col-sac">' + nombreSac(s) + '</th>' + celdas + '</tr>';
    }).join('') : '<tr><td colspan="' + (dias.length + 1) + '"><p class="sac-empty">No hay usuarios SAC activos.</p></td></tr>';
  }

  // =====================================================================
  // 4) PEDIDOS — procesados y cancelados del periodo (tbl_order, por quién cambió el estado)
  // =====================================================================
  var dtPed = null, filasPed = [], reqPed = 0;
  function cargarPedidos(listo) {
    var mio = ++reqPed;
    estadoCarga(true);
    $.ajax({ url: URL_PEDIDOS, data: { desde: clave(F.desde), hasta: clave(F.hasta) }, cache: false })
      .done(function (r) {
        if (mio !== reqPed) return;
        estadoCarga(false);
        var srv = fechaSrv(r.ahora);
        if (srv) { desfase = srv - Date.now(); HOY = soloDia(srv); }
        filasPed = (r.pedidos || []).map(function (p) {
          var rec = fechaSrv(p.recibido), pro = fechaSrv(p.procesado);
          return { id: p.code || p.id, cliente: p.cliente, nombre: p.nombre, ruta: p.ruta, rec: rec, pro: pro, resp: p.resp,
            monto: +p.monto || 0, cancelado: p.estado === 'cancelado', sacId: String(p.sac_id), sac: p.sac, sacUser: p.sac_user };
        });
        agregarProcesadores();
        listo();
      })
      .fail(function (xhr) {
        if (mio !== reqPed) return;
        estadoCarga(false);
        if (xhr && xhr.status === 401) return;
        document.getElementById('sacEqPedKpis').innerHTML = '';
        filasPed = []; pintarTablaPed([]);
        $('#sacEqTblPed').closest('.sac-eq-pane').find('.sac-eq-err').remove();
        $('#sacEqTblPed').closest('.sac-eq-pane').prepend('<div class="sac-eq-err"><p class="sac-empty">' + esc((xhr && xhr.responseJSON && xhr.responseJSON.mensaje) || 'No se pudieron cargar los pedidos.') + ' <button type="button" class="btn btn-link" data-eq-reintentar>Reintentar</button></p></div>');
      });
  }
  // Quien procesa pedidos puede no ser SAC (p. ej. supervisor): se agrega al filtro para poder elegirlo
  function agregarProcesadores() {
    var sel = document.getElementById('sacEqSac'), grupo = document.getElementById('sacEqSacOtros');
    filasPed.forEach(function (p) {
      if (sel.querySelector('option[value="' + p.sacId + '"]')) return;
      if (!grupo) { grupo = document.createElement('optgroup'); grupo.id = 'sacEqSacOtros'; grupo.label = 'También procesan pedidos'; sel.appendChild(grupo); }
      var o = document.createElement('option'); o.value = p.sacId; o.textContent = p.sac; grupo.appendChild(o);
    });
  }
  function personaPed(p) {
    var s = POR_ID[p.sacId] || { nombre: p.sac, username: p.sacUser, ini: (p.sac || '?').split(/\s+/).map(function (x) { return x.charAt(0); }).slice(0, 2).join('').toUpperCase() };
    return nombreSac(s);
  }
  function horaPed(d, base) {
    if (!d) return '—';
    var h = hhmm(d.getHours() * 60 + d.getMinutes());
    return base && !mismoDia(d, base) ? fechaCorta(d) + ' ' + h : h;
  }
  function pintarTablaPed(filas) {
    if (dtPed) { dtPed.clear().rows.add(filas).draw(); return; }
    dtPed = $('#sacEqTblPed').DataTable({
      data: filas, deferRender: true, pageLength: 15, lengthMenu: [15, 30, 50, 100], order: [[5, 'desc']],
      columnDefs: [{ targets: [6, 7], className: 'text-end' }],
      language: lenguaje('pedidos'),
      dom: '<"sac-dt-top"f l>rt<"sac-dt-foot"ip>'
    });
  }
  function pintarPedidos() {
    $('.sac-eq-err').remove();
    var lista = F.sac ? filasPed.filter(function (p) { return p.sacId === F.sac; }) : filasPed;
    var ok = lista.filter(function (p) { return !p.cancelado; });
    var monto = ok.reduce(function (a, p) { return a + p.monto; }, 0);
    var conResp = ok.filter(function (p) { return p.resp != null; });
    var resp = conResp.length ? conResp.reduce(function (a, p) { return a + p.resp; }, 0) / conResp.length : 0;
    var lentos = conResp.filter(function (p) { return p.resp > 45; }).length;
    var canc = lista.length - ok.length;
    document.getElementById('sacEqPedKpis').innerHTML =
      kpi('Pedidos procesados', ok.length.toLocaleString('es-NI'), textoPeriodo()) +
      kpi('Cancelados', canc.toLocaleString('es-NI'), lista.length ? Math.round(canc * 100 / lista.length) + '% del total' : '—') +
      kpi('Monto procesado', moneyK(monto), ok.length ? 'Ticket promedio ' + money(monto / ok.length) : '—') +
      kpi('Tiempo de respuesta prom.', conResp.length ? Math.round(resp) + ' min' : '—', lentos + (lentos === 1 ? ' pedido tardó' : ' pedidos tardaron') + ' más de 45 min');

    pintarTablaPed(lista.map(function (p) {
      return [
        '<span class="sac-num fw-semi-bold">' + esc(p.id) + '</span>',
        '<span data-order="' + (p.rec ? clave(p.rec) : '') + '">' + (p.rec ? fechaCorta(p.rec) : '—') + '</span>',
        '<div class="lh-sm"><span class="sac-cli-name d-block">' + esc(p.nombre || p.cliente) + '</span><span class="sac-cli-sub">' + esc(p.cliente) + (p.ruta ? ' · ' + esc(p.ruta) : '') + '</span></div>',
        personaPed(p),
        '<span class="sac-num">' + horaPed(p.rec) + '</span>',
        '<span class="sac-num" data-order="' + (p.pro ? p.pro.getTime() : 0) + '">' + horaPed(p.pro, p.rec) + '</span>',
        p.resp == null ? '<span class="sac-muted">—</span>' : '<span data-order="' + p.resp + '" class="sac-tag ' + (p.resp > 45 ? 'sac-tag-warn' : 'sac-tag-neutral') + '">' + dur(p.resp) + '</span>',
        '<span class="sac-num" data-order="' + p.monto + '">' + money(p.monto) + '</span>',
        p.cancelado ? '<span class="sac-tag sac-tag-bad">Cancelado</span>' : '<span class="sac-tag sac-tag-ok">Procesado</span>'
      ];
    }));
  }

  // =====================================================================
  // 5) APLICACIONES — tiempo por programa (tbl_uso_app; lo envía la app de escritorio cada minuto)
  // =====================================================================
  var CAT = { productivo: 'Productivo', neutral: 'Neutral', improductivo: 'No productivo' };
  var APPS = { filas: [], dias: [], cats: {}, inactivo: '(inactivo)' }, reqApp = 0, MAX_APPS_LISTA = 30, COLS_DET = 4;
  var N_TOP = 6;                                         // aplicaciones con color propio; el resto va en "Otras"
  // Carga de trabajo: límites de tiempo ACTIVO por día (segundos)
  var CARGA = { poca: 4 * 3600, sobre: 7.5 * 3600 };
  var CARGA_NOM = { sobre: 'Sobrecarga', sana: 'Saludable', poca: 'Poca carga' };
  var CARGA_DET = { sobre: 'más de 7 h 30', sana: '4 h a 7 h 30', poca: 'menos de 4 h' };
  function durSeg(s) { return dur(s / 60); }
  function cargarApps(listo) {
    var mio = ++reqApp;
    estadoCarga(true);
    $.ajax({ url: URL_APPS, data: { desde: clave(F.desde), hasta: clave(F.hasta) }, cache: false })
      .done(function (r) {
        if (mio !== reqApp) return;
        estadoCarga(false);
        var srv = fechaSrv(r.ahora);
        if (srv) { desfase = srv - Date.now(); HOY = soloDia(srv); }
        APPS = { filas: r.filas || [], dias: r.dias || [], cats: r.categorias || {}, inactivo: r.inactivo || '(inactivo)' };
        listo();
      })
      .fail(function (xhr) {
        if (mio !== reqApp) return;
        estadoCarga(false);
        if (xhr && xhr.status === 401) return;
        APPS.filas = []; APPS.dias = [];
        pintarApps((xhr && xhr.responseJSON && xhr.responseJSON.mensaje) || 'No se pudo cargar el uso de aplicaciones.');
      });
  }
  // Agrupa lo cargado según el filtro de persona
  function resumenApps() {
    var ids = {}; sacsFiltrados().forEach(function (s) { ids[s.id] = true; });
    var apps = {}, pers = {}, activo = 0, inactivo = 0;
    APPS.filas.forEach(function (f) {
      if (!ids[f.user_id]) return;
      var p = pers[f.user_id] = pers[f.user_id] || { s: POR_ID[f.user_id], activo: 0, inactivo: 0, apps: {} };
      if (f.proceso === APPS.inactivo) { p.inactivo += f.segundos; inactivo += f.segundos; return; }
      var a = apps[f.proceso] = apps[f.proceso] || { proceso: f.proceso, nombre: f.nombre || f.proceso, seg: 0 };
      a.seg += f.segundos; p.activo += f.segundos; activo += f.segundos;
      p.apps[f.proceso] = (p.apps[f.proceso] || 0) + f.segundos;
    });
    var lista = Object.keys(apps).map(function (k) { return apps[k]; }).sort(function (x, y) { return y.seg - x.seg || x.nombre.localeCompare(y.nombre); });
    var personas = Object.keys(pers).map(function (k) { return pers[k]; }).filter(function (p) { return p.s; })
      .sort(function (x, y) { return (y.activo + y.inactivo) - (x.activo + x.inactivo); });
    return { lista: lista, personas: personas, activo: activo, inactivo: inactivo };
  }
  // Tres botones por aplicación; el marcado es la clasificación vigente
  function botonesCat(a) {
    var act = APPS.cats[a.proceso] || '';
    return '<div class="sac-clasif-btns" role="group" aria-label="Clasificación de ' + esc(a.nombre) + '">' +
      Object.keys(CAT).map(function (k) {
        return '<button type="button" class="sac-clasif-btn is-' + k + '" data-app-cat="' + esc(a.proceso) + '" data-cat="' + k + '" aria-pressed="' + (act === k ? 'true' : 'false') + '">' + CAT[k] + '</button>';
      }).join('') + '</div>';
  }
  function barraApp(nombre, extra, seg, total, max, tip) {
    return '<div class="sac-app-fila" data-tip="' + esc(tip) + '">' +
      '<div class="sac-app-cab"><div class="sac-app-nom"><span>' + esc(nombre) + '</span>' + extra + '</div>' +
      '<div class="sac-app-val"><strong>' + durSeg(seg) + '</strong><span>' + (pct(seg, total) == null ? '' : pct(seg, total) + '%') + '</span></div></div>' +
      '<div class="sac-app-track" aria-hidden="true"><i style="width:' + (max > 0 ? Math.max(1, seg * 100 / max).toFixed(1) : 0) + '%"></i></div></div>';
  }
  // Ranking de todo el equipo: fija el color de cada aplicación para que no cambie al filtrar por persona
  function colorPorApp() {
    var tot = {};
    APPS.filas.forEach(function (f) { if (f.proceso !== APPS.inactivo && POR_ID[f.user_id]) tot[f.proceso] = (tot[f.proceso] || 0) + f.segundos; });
    var mapa = {};
    Object.keys(tot).sort(function (a, b) { return tot[b] - tot[a] || a.localeCompare(b); }).slice(0, N_TOP).forEach(function (p, i) { mapa[p] = i + 1; });
    return mapa;
  }

  // Aplicaciones más usadas: lista de píldoras + dona
  function pintarTop(r) {
    var top = r.lista.slice(0, N_TOP), resto = r.lista.slice(N_TOP);
    var fijo = colorPorApp(), usados = {}, libres = [];
    top.forEach(function (a) { if (fijo[a.proceso]) usados[fijo[a.proceso]] = true; });
    for (var n = 1; n <= N_TOP; n++) if (!usados[n]) libres.push(n);
    var items = top.map(function (a) {
      return { nombre: a.nombre, seg: a.seg, slot: fijo[a.proceso] || libres.shift(),
        tip: '<strong>' + esc(a.nombre) + '</strong><span>' + esc(a.proceso) + '</span><span>' + durSeg(a.seg) + ' · ' + pct(a.seg, r.activo) + '% del tiempo activo</span>' };
    });
    var segResto = resto.reduce(function (s, x) { return s + x.seg; }, 0);
    if (resto.length) items.push({ nombre: 'Otras ' + resto.length + (resto.length === 1 ? ' aplicación' : ' aplicaciones'), seg: segResto, slot: 0,
      tip: '<strong>Otras aplicaciones</strong><span>' + esc(resto.slice(0, 6).map(function (x) { return x.nombre; }).join(', ')) + (resto.length > 6 ? '…' : '') + '</span><span>' + durSeg(segResto) + ' · ' + pct(segResto, r.activo) + '% del tiempo activo</span>' });

    document.getElementById('sacEqTopLista').innerHTML = items.map(function (it) {
      return '<div class="sac-apt-pill is-s' + it.slot + '" data-tip="' + esc(it.tip) + '"><i aria-hidden="true"></i><span class="sac-apt-nom">' + esc(it.nombre) + '</span>' +
        '<strong>' + durSeg(it.seg) + '</strong><span class="sac-apt-pct">' + pct(it.seg, r.activo) + '%</span></div>';
    }).join('');

    // Dona: circunferencia = 100, así cada tramo mide su porcentaje; se deja un hueco entre tramos
    var acum = 0, hueco = items.length > 1 ? 0.7 : 0;
    var arcos = items.map(function (it) {
      var p = it.seg * 100 / r.activo, largo = Math.max(0.4, p - hueco);
      var arco = '<circle class="is-s' + it.slot + '" cx="21" cy="21" r="15.9155" fill="none" stroke-width="7" stroke-dasharray="' + largo.toFixed(2) + ' ' + (100 - largo).toFixed(2) + '" stroke-dashoffset="' + (25 - acum).toFixed(2) + '" data-tip="' + esc(it.tip) + '"></circle>';
      acum += p;
      return arco;
    }).join('');
    document.getElementById('sacEqTopDona').innerHTML =
      '<svg viewBox="0 0 42 42" role="img" aria-label="Reparto del tiempo activo por aplicación">' + arcos + '</svg>' +
      '<div class="sac-apt-centro"><strong>' + durSeg(r.activo) + '</strong><span>tiempo activo</span></div>';
  }

  // Carga de trabajo: cuántas personas hubo cada día en cada nivel, según su tiempo activo
  function pintarCarga() {
    var ids = {}; sacsFiltrados().forEach(function (s) { ids[s.id] = true; });
    var fin = soloDia(F.hasta), ini = soloDia(F.desde), min7 = sumarDias(fin, -6);
    if (ini > min7) ini = min7;
    if (sumarDias(fin, -30) > ini) ini = sumarDias(fin, -30);
    var porDia = {};
    APPS.dias.forEach(function (x) { if (ids[x.user_id]) (porDia[x.fecha] = porDia[x.fecha] || []).push(x); });
    var cols = [], max = 0;
    for (var d = new Date(ini); d <= fin; d = sumarDias(d, 1)) {
      var c = { d: new Date(d), sobre: 0, sana: 0, poca: 0, quien: { sobre: [], sana: [], poca: [] } };
      (porDia[clave(d)] || []).forEach(function (x) {
        var k = x.activo > CARGA.sobre ? 'sobre' : x.activo >= CARGA.poca ? 'sana' : 'poca';
        c[k]++; c.quien[k].push(((POR_ID[x.user_id] || {}).nombre || '?') + ' · ' + durSeg(x.activo));
      });
      c.total = c.sobre + c.sana + c.poca;
      max = Math.max(max, c.total);
      cols.push(c);
    }
    var cada = Math.ceil(cols.length / 10);
    // Eje: de 0 a un tope "redondo" (personas), con una línea guía por marca
    var tope = Math.max(1, max), paso = tope <= 5 ? 1 : Math.ceil(tope / 4);
    tope = Math.ceil(tope / paso) * paso;
    max = tope;
    var guias = '';
    for (var g = 0; g <= tope; g += paso) guias += '<b style="bottom:' + (g * 100 / tope).toFixed(1) + '%"><span>' + g + '</span></b>';
    document.getElementById('sacEqCarga').innerHTML = '<div class="sac-carga-cols' + (cols.length > 10 ? ' is-denso' : '') + '"><div class="sac-carga-guias" aria-hidden="true">' + guias + '</div>' + cols.map(function (c, i) {
      var hoy = mismoDia(c.d, HOY);
      var seg = ['sobre', 'sana', 'poca'].map(function (k) {
        if (!c[k]) return '';
        var tip = '<strong>' + CARGA_NOM[k] + ' · ' + fechaCorta(c.d) + (hoy ? ' (en curso)' : '') + '</strong>' + c.quien[k].map(function (q) { return '<span>' + esc(q) + '</span>'; }).join('');
        return '<i class="is-' + k + '" style="height:' + (c[k] * 100 / max).toFixed(1) + '%" data-tip="' + esc(tip) + '">' + c[k] + '</i>';
      }).join('');
      var etiqueta = (i % cada === 0 || i === cols.length - 1) ? fechaCorta(c.d) + (hoy ? '<small>en curso</small>' : '') : '';
      return '<div class="sac-carga-col' + (hoy ? ' is-hoy' : '') + '"><div class="sac-carga-barra">' + (seg || '<b class="sac-carga-nada" aria-label="Sin registros"></b>') + '</div><span class="sac-carga-dia">' + etiqueta + '</span></div>';
    }).join('') + '</div>';
    document.getElementById('sacEqCargaLey').innerHTML = ['sobre', 'sana', 'poca'].map(function (k) {
      return '<span><i class="is-' + k + '" aria-hidden="true"></i>' + CARGA_NOM[k] + ' · ' + CARGA_DET[k] + '</span>';
    }).join('');
  }

  function pintarApps(error) {
    $('.sac-eq-err').remove();
    var r = resumenApps(), medido = r.activo + r.inactivo;
    var kp = document.getElementById('sacEqAppKpis'), li = document.getElementById('sacEqAppLista'),
        pe = document.getElementById('sacEqAppPersonas'), de = document.getElementById('sacEqAppDet'),
        tl = document.getElementById('sacEqTopLista'), td = document.getElementById('sacEqTopDona');
    document.getElementById('sacEqAppSub').textContent = '';
    pintarCarga();
    if (error || !medido) {
      var msg = error ? esc(error) + ' <button type="button" class="btn btn-link" data-eq-reintentar>Reintentar</button>'
        : 'Todavía no hay registros de uso en este periodo. Los envía la app de escritorio mientras el SAC está En línea o AFK.';
      kp.innerHTML = ''; pe.innerHTML = ''; de.innerHTML = ''; li.innerHTML = ''; td.innerHTML = '';
      tl.innerHTML = '<p class="sac-empty">' + msg + '</p>';
      return;
    }
    var top = r.lista[0];
    kp.innerHTML =
      kpi('Tiempo medido', durSeg(medido), 'En línea y AFK de ' + r.personas.length + (r.personas.length === 1 ? ' persona' : ' personas') + ' · ' + textoPeriodo()) +
      kpi('Tiempo activo', durSeg(r.activo), pct(r.activo, medido) + '% del tiempo medido, usando teclado o mouse') +
      kpi('Sin actividad', durSeg(r.inactivo), pct(r.inactivo, medido) + '%, más de 5 min sin teclado ni mouse') +
      kpi('Aplicación más usada', top ? esc(top.nombre) : '—', top ? durSeg(top.seg) + ', ' + pct(top.seg, r.activo) + '% del tiempo activo' : '');

    // Reparto por categoría (texto: la categoría no lleva color)
    var porCat = { productivo: 0, neutral: 0, improductivo: 0 }, sinCat = 0;
    r.lista.forEach(function (a) { var c = APPS.cats[a.proceso]; if (c && porCat[c] != null) porCat[c] += a.seg; else sinCat += a.seg; });
    document.getElementById('sacEqAppSub').textContent = Object.keys(CAT).map(function (k) { return CAT[k] + ' ' + (pct(porCat[k], r.activo) || 0) + '%'; }).join(' · ') +
      (sinCat ? ' · Sin clasificar ' + pct(sinCat, r.activo) + '%' : '');

    if (r.activo) pintarTop(r); else { tl.innerHTML = '<p class="sac-empty">Solo hay tiempo sin actividad en este periodo.</p>'; td.innerHTML = ''; }

    // Clasificar: una fila por aplicación con sus tres botones
    li.innerHTML = r.lista.slice(0, MAX_APPS_LISTA).map(function (a) {
      return '<div class="sac-clasif-fila"><div class="sac-clasif-app"><span class="sac-clasif-nom">' + esc(a.nombre) + '</span>' +
        '<span class="sac-clasif-sub">' + esc(a.proceso) + ' · ' + durSeg(a.seg) + '</span></div>' + botonesCat(a) + '</div>';
    }).join('');

    pe.innerHTML = r.personas.map(function (p) {
      var tot = p.activo + p.inactivo, k = Object.keys(p.apps).sort(function (x, y) { return p.apps[y] - p.apps[x]; })[0];
      var topP = k ? (r.lista.filter(function (a) { return a.proceso === k; })[0] || {}).nombre : null;
      return '<div class="sac-app-fila" data-tip="' + esc('<strong>' + esc(p.s.nombre) + '</strong><span>Activo ' + durSeg(p.activo) + ' · ' + pct(p.activo, tot) + '%</span><span>Sin actividad ' + durSeg(p.inactivo) + ' · ' + pct(p.inactivo, tot) + '%</span>') + '">' +
        '<div class="sac-app-cab"><div class="sac-app-nom"><span>' + esc(p.s.nombre) + '</span><small>' + esc(p.s.username) + '</small></div>' +
        '<div class="sac-app-val"><strong>' + durSeg(tot) + '</strong></div></div>' +
        '<div class="sac-app-split" aria-hidden="true"><i class="is-act" style="width:' + (p.activo * 100 / tot).toFixed(1) + '%"></i><i class="is-ina"></i></div>' +
        '<div class="sac-app-sub"><span>Activo ' + durSeg(p.activo) + ' · Sin actividad ' + durSeg(p.inactivo) + '</span>' + (topP ? '<span>Más usada: ' + esc(topP) + '</span>' : '') + '</div></div>';
    }).join('');

    var cols = r.lista.slice(0, COLS_DET);
    de.innerHTML = '<thead><tr><th scope="col">Persona</th>' + cols.map(function (a) { return '<th scope="col" class="text-end">' + esc(a.nombre) + '</th>'; }).join('') +
      '<th scope="col" class="text-end">Otras</th><th scope="col" class="text-end">Activo</th></tr></thead><tbody>' +
      r.personas.map(function (p) {
        var enCols = 0;
        var tds = cols.map(function (a) { var v = p.apps[a.proceso] || 0; enCols += v; return '<td class="text-end sac-num">' + (v ? durSeg(v) : '<span class="sac-muted">—</span>') + '</td>'; }).join('');
        var otras = p.activo - enCols;
        return '<tr><th scope="row">' + nombreSac(p.s) + '</th>' + tds + '<td class="text-end sac-num">' + (otras ? durSeg(otras) : '<span class="sac-muted">—</span>') + '</td><td class="text-end sac-num fw-bold">' + durSeg(p.activo) + '</td></tr>';
      }).join('') + '</tbody>';
  }
  // Clasificar una aplicación (vale para todo el equipo)
  document.addEventListener('click', function (e) {
    var b = e.target.closest ? e.target.closest('button[data-app-cat]') : null;
    if (!b || b.getAttribute('aria-pressed') === 'true') return;
    var proceso = b.getAttribute('data-app-cat'), cat = b.getAttribute('data-cat'), grupo = b.parentNode.querySelectorAll('button');
    grupo.forEach(function (x) { x.disabled = true; });
    $.ajax({ url: URL_APP_CAT, method: 'POST', data: { proceso: proceso, categoria: cat },
      headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content } })
      .done(function () { APPS.cats[proceso] = cat; if (F.tab === 'aplicaciones') pintarApps(); })
      .fail(function (xhr) {
        grupo.forEach(function (x) { x.disabled = false; });
        if (xhr && xhr.status === 401) return;
        alert((xhr && xhr.responseJSON && xhr.responseJSON.mensaje) || 'No se pudo guardar la clasificación.');
      });
  });
  function lenguaje(que) {
    return {
      search: '', searchPlaceholder: 'Buscar…', lengthMenu: '_MENU_ filas',
      info: '_START_–_END_ de _TOTAL_ ' + que, infoEmpty: 'Sin ' + que, infoFiltered: '(de _MAX_)',
      zeroRecords: 'No hay resultados que coincidan', emptyTable: 'No hay ' + que + ' en este periodo',
      paginate: { previous: '‹', next: '›', first: '«', last: '»' }
    };
  }
  function textoPeriodo() {
    if (mismoDia(F.desde, F.hasta)) return mismoDia(F.desde, HOY) ? 'Hoy, hasta las ' + hhmm(ahoraMin()) : fechaCorta(F.desde);
    return fechaCorta(F.desde) + ' – ' + fechaCorta(F.hasta);
  }

  // =====================================================================
  // Pestañas, filtros y exportación
  // =====================================================================
  var PINTAR = { vivo: pintarVivo, bitacora: pintarBitacora, resumen: pintarResumen, pedidos: pintarPedidos, aplicaciones: pintarApps };
  function refrescar() {
    if (F.tab === 'aplicaciones') { cargarApps(function () { if (F.tab === 'aplicaciones') pintarApps(); }); return; }
    if (F.tab === 'pedidos') { cargarPedidos(function () { if (F.tab === 'pedidos') pintarPedidos(); }); return; }
    var tab = F.tab, a = F.desde, b = F.hasta;
    if (tab === 'vivo') { a = HOY; b = HOY; }
    if (tab === 'resumen') { a = F.resDesde; b = F.resHasta; }
    cargar(a, b, function () { if (F.tab === tab) PINTAR[tab](); });
  }

  document.querySelectorAll('[data-eq-tab]').forEach(function (b) {
    b.addEventListener('click', function () {
      F.tab = b.getAttribute('data-eq-tab');
      document.querySelectorAll('[data-eq-tab]').forEach(function (x) { var on = x === b; x.classList.toggle('active', on); x.setAttribute('aria-selected', on ? 'true' : 'false'); });
      document.querySelectorAll('[data-eq-pane]').forEach(function (p) { p.hidden = p.getAttribute('data-eq-pane') !== F.tab; });
      document.querySelectorAll('[data-eq-oculto]').forEach(function (el) { el.hidden = el.getAttribute('data-eq-oculto').split(' ').indexOf(F.tab) !== -1; });
      document.querySelectorAll('[data-eq-solo]').forEach(function (el) { el.hidden = el.getAttribute('data-eq-solo') !== F.tab; });
      document.getElementById('sacEqSac').hidden = F.tab === 'vivo';
      try { history.replaceState(null, '', '#' + F.tab); } catch (e) {}
      refrescar();
      if (F.tab === 'bitacora' && dtBit) dtBit.columns.adjust();
      if (F.tab === 'pedidos' && dtPed) dtPed.columns.adjust();
    });
  });

  var sel = document.getElementById('sacEqSac');
  SACS.slice().sort(function (a, b) { return a.nombre.localeCompare(b.nombre); }).forEach(function (s) {
    var o = document.createElement('option'); o.value = s.id; o.textContent = s.nombre; sel.appendChild(o);
  });
  // El filtro de SAC se aplica sobre los datos ya cargados
  sel.addEventListener('change', function () { F.sac = sel.value; if (F.tab === 'pedidos') pintarPedidos(); else if (F.tab === 'aplicaciones') pintarApps(); else if (!DATOS.cargado) refrescar(); else PINTAR[F.tab](); });

  // Fechas: dos selectores separados, como en Pedidos
  var ES = {
    weekdays: { shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'], longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] },
    months: { shorthand: MESES, longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] },
    firstDayOfWeek: 1, ordinal: function () { return ''; }
  };
  var fpD = null, fpH = null;
  function marcarPreset(p) { document.querySelectorAll('[data-eq-preset]').forEach(function (b) { b.classList.toggle('on', b.getAttribute('data-eq-preset') === p); }); }
  function fijar(desde, hasta, preset) {
    F.desde = soloDia(desde); F.hasta = soloDia(hasta);
    if (fpD) { fpD.setDate(F.desde, false); fpH.setDate(F.hasta, false); fpH.set('minDate', F.desde); fpD.set('maxDate', F.hasta); }
    marcarPreset(preset || '');
    refrescar();
  }
  if (window.flatpickr) {
    var base = { locale: ES, dateFormat: 'Y-m-d', altInput: true, altFormat: 'j M Y', maxDate: HOY, disableMobile: true };
    fpD = flatpickr('#sacEqDesde', Object.assign({}, base, { defaultDate: HOY, onChange: function (d) { if (d[0]) fijar(d[0], F.hasta < d[0] ? d[0] : F.hasta); } }));
    fpH = flatpickr('#sacEqHasta', Object.assign({}, base, { defaultDate: HOY, onChange: function (d) { if (d[0]) fijar(F.desde > d[0] ? d[0] : F.desde, d[0]); } }));
  }
  document.querySelectorAll('[data-eq-preset]').forEach(function (b) {
    b.addEventListener('click', function () {
      var p = b.getAttribute('data-eq-preset'), a = new Date(HOY), z = new Date(HOY);
      if (p === 'ayer') { a.setDate(a.getDate() - 1); z = new Date(a); }
      else if (p === '7') a.setDate(a.getDate() - 6);
      else if (p === 'mes') a.setDate(1);
      fijar(a, z, p);
    });
  });

  // Resumen: dos fechas (Desde / Hasta), por defecto la semana actual. Máximo MAX_DIAS_RES días.
  var fpRD = null, fpRH = null;
  function fijarResumen(desde, hasta) {
    desde = soloDia(desde); hasta = soloDia(hasta);
    if (hasta < desde) hasta = new Date(desde);
    if (sumarDias(desde, MAX_DIAS_RES - 1) < hasta) hasta = sumarDias(desde, MAX_DIAS_RES - 1);
    F.resDesde = desde; F.resHasta = hasta;
    if (fpRD) {
      fpRD.setDate(desde, false); fpRH.setDate(hasta, false);
      fpRH.set('minDate', desde); fpRH.set('maxDate', sumarDias(desde, MAX_DIAS_RES - 1) < FIN_SEMANA ? sumarDias(desde, MAX_DIAS_RES - 1) : FIN_SEMANA);
      fpRD.set('maxDate', hasta);
    }
    var lun = lunesDe(HOY);
    document.getElementById('sacEqResSemana').classList.toggle('on', mismoDia(desde, lun) && mismoDia(hasta, sumarDias(lun, 6)));
    if (F.tab === 'resumen') refrescar();
  }
  var FIN_SEMANA = sumarDias(lunesDe(HOY), 6);
  F.resDesde = lunesDe(HOY); F.resHasta = FIN_SEMANA;
  if (window.flatpickr) {
    var baseR = { locale: ES, dateFormat: 'Y-m-d', altInput: true, altFormat: 'j M Y', disableMobile: true };
    fpRD = flatpickr('#sacEqResDesde', Object.assign({}, baseR, { defaultDate: F.resDesde, maxDate: F.resHasta, onChange: function (d) { if (d[0]) fijarResumen(d[0], F.resHasta); } }));
    fpRH = flatpickr('#sacEqResHasta', Object.assign({}, baseR, { defaultDate: F.resHasta, minDate: F.resDesde, maxDate: FIN_SEMANA, onChange: function (d) { if (d[0]) fijarResumen(F.resDesde, d[0]); } }));
  }
  document.getElementById('sacEqResSemana').addEventListener('click', function () { fijarResumen(lunesDe(HOY), sumarDias(lunesDe(HOY), 6)); });

  // Exportar la pestaña actual a CSV (Excel lo abre directamente)
  document.getElementById('sacEqExport').addEventListener('click', function () {
    // Resumen: Excel corporativo generado en el servidor, con la misma estructura del cuadro
    if (F.tab === 'resumen') {
      location.href = URL_EXCEL + '?' + $.param({ desde: clave(F.resDesde), hasta: clave(F.resHasta), sac: F.sac || '' });
      return;
    }
    var filas = [];
    if (F.tab === 'vivo') {
      filas.push(['SAC', 'Usuario', 'Estado', 'Desde', 'Minutos en el estado', 'En línea hoy (h)', 'AFK hoy (h)', 'Descanso hoy (h)']);
      tarjetasVivo().forEach(function (c) {
        filas.push([c.s.nombre, c.s.username, NOMBRE[c.e], c.act ? hhmm(c.act.a) : (c.ult ? hhmm(c.ult.b) : ''), c.act ? Math.round(c.now - c.act.a) : '',
          (c.t.on / 60).toFixed(2), (c.t.afk / 60).toFixed(2), (c.t.lunch / 60).toFixed(2)]);
      });
    } else if (F.tab === 'bitacora') {
      filas.push(['Fecha', 'Hora', 'SAC', 'De', 'A', 'Duración (min)', 'Origen']);
      filasBit.forEach(function (r) { filas.push([clave(r.d), hhmm(r.min), r.s.nombre, NOMBRE[r.de], NOMBRE[r.a], r.dur == null ? '' : Math.round(r.dur), ORIGEN[r.origen] || 'Manual']); });
    } else if (F.tab === 'pedidos') {
      filas.push(['Pedido', 'Código cliente', 'Cliente', 'Ruta', 'Procesado por', 'Recibido', 'Procesado', 'Respuesta (min)', 'Monto', 'Estado']);
      var fh = function (d) { return d ? clave(d) + ' ' + hhmm(d.getHours() * 60 + d.getMinutes()) : ''; };
      (F.sac ? filasPed.filter(function (p) { return p.sacId === F.sac; }) : filasPed).forEach(function (p) {
        filas.push([p.id, p.cliente, p.nombre, p.ruta, p.sac, fh(p.rec), fh(p.pro), p.resp == null ? '' : Math.round(p.resp), p.monto.toFixed(2), p.cancelado ? 'Cancelado' : 'Procesado']);
      });
    }
    if (F.tab === 'aplicaciones') {
      filas.push(['SAC', 'Usuario', 'Aplicación', 'Programa', 'Categoría', 'Minutos']);
      var visibles = {}; sacsFiltrados().forEach(function (s) { visibles[s.id] = s; });
      APPS.filas.filter(function (f) { return visibles[f.user_id]; })
        .sort(function (x, y) { return visibles[x.user_id].nombre.localeCompare(visibles[y.user_id].nombre) || y.segundos - x.segundos; })
        .forEach(function (f) {
          var ina = f.proceso === APPS.inactivo;
          filas.push([visibles[f.user_id].nombre, visibles[f.user_id].username, ina ? 'Sin actividad' : f.nombre, ina ? '' : f.proceso,
            ina ? '' : (CAT[APPS.cats[f.proceso]] || 'Sin clasificar'), (f.segundos / 60).toFixed(1)]);
        });
    }
    var csv = '﻿' + filas.map(function (f) { return f.map(function (v) { v = String(v); return /[",;\n]/.test(v) ? '"' + v.replace(/"/g, '""') + '"' : v; }).join(','); }).join('\r\n');
    var url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
    var a = document.createElement('a');
    var rango = F.tab === 'vivo' ? clave(HOY) : F.tab === 'resumen' ? clave(F.resDesde) + (mismoDia(F.resDesde, F.resHasta) ? '' : '_' + clave(F.resHasta)) : clave(F.desde) + (mismoDia(F.desde, F.hasta) ? '' : '_' + clave(F.hasta));
    a.href = url; a.download = 'equipo-sac-' + F.tab + '-' + rango + '.csv';
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
  });

  // Pestaña inicial (permite enlazar #bitacora, #resumen, etc.)
  var inicial = (location.hash || '').replace('#', '');
  var btnIni = document.querySelector('[data-eq-tab="' + (PINTAR[inicial] ? inicial : 'vivo') + '"]');
  btnIni.click();

  // En vivo se actualiza solo cada minuto
  setInterval(function () { if (F.tab === 'vivo' && !document.hidden) refrescar(); }, 60000);
})();
</script>
