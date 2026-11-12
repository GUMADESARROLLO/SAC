<script>
/* Equipo SAC: VISTA PREVIA con datos simulados.
   Todo lo que se ve aquí se genera en el navegador a partir de una semilla por SAC y fecha,
   así los números no cambian al recargar. La versión real leerá tbl_estado_log y tbl_order.updated_by. */
(function () {
  'use strict';

  var NOMBRE = { on: 'En línea', afk: 'AFK', lunch: 'Almuerzo', off: 'Desconectado' };
  var ORDEN_ESTADO = { on: 0, afk: 1, lunch: 2, off: 3 };
  var MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
  var DIA0 = 8 * 60, DIA1 = 18 * 60;   // eje del día: 08:00 a 18:00

  // ---------- Equipo (nombres reales si existen; si no, de ejemplo) ----------
  var SACS = (window.SAC_EQ_SACS || []).slice(0, 14);
  if (!SACS.length) {
    SACS = ['Reyna Alvarez', 'Karla Mendoza', 'José Ruiz', 'María Gutiérrez', 'Ana Lucía Pérez', 'Carlos Téllez', 'Sofía Chavarría', 'Luis Obando']
      .map(function (n, i) { return { id: i + 1, nombre: n, username: 'SAC' + (i < 9 ? '0' : '') + (i + 1) }; });
  }
  SACS.forEach(function (s, i) {
    var p = (s.nombre || '?').split(/\s+/).filter(Boolean);
    s.ini = ((p[0] || '?').charAt(0) + (p[1] || '').charAt(0)).toUpperCase();
    // Perfil estable: ritmo de pedidos y tiempo de respuesta propios
    var r = rng(hash('perfil' + s.id));
    s.ritmo = 2.6 + r() * 2.6;        // pedidos por hora en línea
    s.resp = 12 + r() * 26;           // minutos promedio de respuesta
    s.afk = 1 + Math.floor(r() * 4);  // pausas AFK por día
  });

  var CLIENTES = ['Farmacia San Martín', 'Farmacia La Salud', 'Farmacia Central', 'Distribuidora El Buen Pastor', 'Farmacia Monte Sinaí',
    'Clínica Santa Fe', 'Farmacia Divino Niño', 'Farmacia Las Américas', 'Farmacia El Carmen', 'Hospital Privado Metropolitano',
    'Farmacia Popular', 'Farmacia Santa Ana', 'Farmacia Medicentro', 'Farmacia San Judas', 'Farmacia La Fe', 'Clínica Médica del Norte',
    'Farmacia Xolotlán', 'Farmacia Los Robles', 'Farmacia Altamira', 'Farmacia El Progreso', 'Farmacia Masaya Centro', 'Farmacia León Santo Cristo'];

  // ---------- Utilidades ----------
  function hash(str) { var h = 2166136261; for (var i = 0; i < str.length; i++) { h ^= str.charCodeAt(i); h = Math.imul(h, 16777619); } return h >>> 0; }
  function rng(a) { return function () { a |= 0; a = a + 0x6D2B79F5 | 0; var t = Math.imul(a ^ a >>> 15, 1 | a); t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t; return ((t ^ t >>> 14) >>> 0) / 4294967296; }; }
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

  // "Ahora" del panel en vivo: si se abre fuera del horario, se simula a las 15:20 para que la vista tenga contenido
  var REAL = new Date(), minReal = REAL.getHours() * 60 + REAL.getMinutes();
  var FUERA_HORARIO = minReal < 8 * 60 + 30 || minReal > 17 * 60 + 45 || REAL.getDay() === 0;
  var AHORA_MIN = FUERA_HORARIO ? 15 * 60 + 20 : minReal;

  // ---------- Generación del día de un SAC ----------
  var cache = {};
  function dia(sac, fecha, corte) {
    var k = sac.id + '|' + clave(fecha) + '|' + corte;
    if (cache[k]) return cache[k];
    var r = rng(hash(sac.id + '|' + clave(fecha)));
    var dow = fecha.getDay(), res = { tramos: [], pedidos: [] };
    if (dow === 0 || r() < 0.05) return (cache[k] = res);   // domingo o ausencia

    // Variedad para la vista previa de hoy: algunos SAC en AFK, almuerzo o sin marcar en este momento
    var idx = SACS.indexOf(sac), forzada = null;
    if (corte != null && corte > DIA0 + 90) {
      if (idx % 7 === 5) return (cache[k] = res);
      if (idx % 7 === 1) forzada = { e: 'afk', a: corte - 18, b: corte + 20 };
      if (idx % 7 === 3) forzada = { e: 'lunch', a: corte - 26, b: corte + 30 };
      if (idx % 7 === 6) forzada = { e: 'afk', a: corte - 6, b: corte + 10 };
    }

    var sab = dow === 6;
    var ini = DIA0 + Math.round(-6 + r() * 30);
    var fin = sab ? 12 * 60 + Math.round(r() * 15) : 17 * 60 + 30 + Math.round(-15 + r() * 30);
    if (forzada && (forzada.a < ini + 30 || forzada.b > fin)) forzada = null;
    var pausas = forzada ? [forzada] : [];
    var l0 = 12 * 60 + Math.round(-15 + r() * 45), l1 = l0 + 38 + Math.round(r() * 30);
    if (!sab && !(forzada && forzada.e === 'lunch') && !(forzada && l0 < forzada.b + 15 && l1 > forzada.a - 15)) pausas.push({ e: 'lunch', a: l0, b: l1 });
    var nAfk = sab ? 1 : sac.afk + (r() < 0.3 ? 1 : 0);
    for (var i = 0; i < nAfk; i++) {
      var a = ini + 40 + Math.round(r() * (fin - ini - 80)), d = 4 + Math.round(r() * r() * 26);
      if (!pausas.some(function (p) { return a < p.b + 15 && a + d > p.a - 15; })) pausas.push({ e: 'afk', a: a, b: a + d });
    }
    pausas.sort(function (x, y) { return x.a - y.a; });

    var t = ini;
    pausas.forEach(function (p) { if (p.a > t) res.tramos.push({ e: 'on', a: t, b: p.a }); res.tramos.push({ e: p.e, a: p.a, b: p.b }); t = p.b; });
    if (fin > t) res.tramos.push({ e: 'on', a: t, b: fin });
    res.tramos.push({ e: 'off', a: fin, b: DIA1 + 120 });

    // Recorte a "ahora" (solo para hoy)
    if (corte != null) {
      res.tramos = res.tramos.filter(function (x) { return x.a < corte; }).map(function (x) { return { e: x.e, a: x.a, b: Math.min(x.b, corte), abierto: x.b > corte }; });
    }

    // Pedidos: procesados dentro de sus tramos En línea
    var nPed = 0;
    res.tramos.forEach(function (x) {
      if (x.e !== 'on') return;
      var n = Math.round((x.b - x.a) / 60 * sac.ritmo * (0.8 + r() * 0.4));
      for (var j = 0; j < n; j++) {
        var proc = x.a + r() * (x.b - x.a);
        var resp = Math.max(2, Math.round(sac.resp * (0.35 + r() * r() * 2.6)));
        var anulado = r() < 0.06;
        var monto = Math.round((1500 + Math.pow(r(), 2.2) * 85000) * 100) / 100;
        res.pedidos.push({
          id: 'PED-' + (100000 + (hash(sac.id + clave(fecha) + nPed++) % 900000)),
          fecha: fecha, sac: sac, cliente: CLIENTES[Math.floor(r() * CLIENTES.length)],
          recibido: proc - resp, procesado: proc, resp: resp, monto: monto, anulado: anulado
        });
      }
    });
    return (cache[k] = res);
  }

  function totalesDia(tr) {
    var t = { on: 0, afk: 0, lunch: 0, off: 0, cambios: 0 };
    tr.forEach(function (x) { if (x.e !== 'off' || !x.abierto) t[x.e] += x.b - x.a; });
    t.off = 0;
    t.cambios = tr.length;
    return t;
  }

  // ---------- Estado de filtros ----------
  var HOY = soloDia(REAL);
  var F = { tab: 'vivo', desde: HOY, hasta: HOY, sac: '' };

  function diasDelRango() {
    var out = [], d = new Date(F.desde);
    while (d <= F.hasta && out.length < 62) { out.push(new Date(d)); d.setDate(d.getDate() + 1); }
    return out;
  }
  function sacsFiltrados() { return F.sac ? SACS.filter(function (s) { return String(s.id) === F.sac; }) : SACS; }
  function corteDe(d) { return mismoDia(d, HOY) ? AHORA_MIN : null; }

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

  // =====================================================================
  // 1) EN VIVO
  // =====================================================================
  function pintarVivo() {
    var cnt = { on: 0, afk: 0, lunch: 0, off: 0 }, tOn = 0, tBase = 0, tarjetas = [];
    SACS.forEach(function (s) {
      var d = dia(s, HOY, AHORA_MIN), tr = d.tramos, act = tr.length ? tr[tr.length - 1] : null;
      var e = act ? act.e : 'off';
      cnt[e]++;
      var t = totalesDia(tr); tOn += t.on; tBase += t.on + t.afk + t.lunch;
      var ped = d.pedidos.filter(function (p) { return !p.anulado; });
      var resp = ped.length ? ped.reduce(function (a, p) { return a + p.resp; }, 0) / ped.length : 0;
      tarjetas.push({ s: s, e: e, act: act, tr: tr, t: t, ped: ped.length, resp: resp });
    });
    tarjetas.sort(function (a, b) { return ORDEN_ESTADO[a.e] - ORDEN_ESTADO[b.e] || a.s.nombre.localeCompare(b.s.nombre); });

    var n = SACS.length, ef = pct(tOn, tBase);
    document.getElementById('sacEqVivoKpis').innerHTML =
      kpi('<i class="sac-est-dot is-on" aria-hidden="true"></i> En línea', cnt.on + '<span class="sac-kv-of"> / ' + n + '</span>', 'Atendiendo pedidos ahora') +
      kpi('<i class="sac-est-dot is-afk" aria-hidden="true"></i> AFK', cnt.afk, 'Ausentes un momento') +
      kpi('<i class="sac-est-dot is-lunch" aria-hidden="true"></i> Almuerzo', cnt.lunch, 'En pausa de comida') +
      kpi('<i class="sac-est-dot is-off" aria-hidden="true"></i> Desconectados', cnt.off, 'Sin turno o sin marcar') +
      kpi('Tiempo efectivo del equipo', ef == null ? '—' : ef + '%', 'Hoy, hasta las ' + hhmm(AHORA_MIN), '<div class="sac-bar"><span style="width:' + (ef || 0) + '%"></span></div>');

    var ticks = '';
    for (var h = 8; h <= 18; h += 2) ticks += '<span style="left:' + ((h * 60 - DIA0) * 100 / (DIA1 - DIA0)) + '%">' + h + '</span>';
    var span = DIA1 - DIA0;

    document.getElementById('sacEqGrid').innerHTML = tarjetas.map(function (c) {
      var seg = c.tr.map(function (x) {
        var a = Math.max(x.a, DIA0), b = Math.min(x.b, DIA1);
        if (b <= a || x.e === 'off') return '';
        return '<i class="is-' + x.e + '" style="left:' + ((a - DIA0) * 100 / span).toFixed(2) + '%;width:calc(' + ((b - a) * 100 / span).toFixed(2) + '% - 2px)" data-tip="' + esc(tipTramo(c.s, x)) + '"></i>';
      }).join('');
      var ahoraPos = ((Math.min(Math.max(AHORA_MIN, DIA0), DIA1) - DIA0) * 100 / span).toFixed(2);
      var ef = pct(c.t.on, c.t.on + c.t.afk + c.t.lunch);
      var desde = c.act && c.e !== 'off' ? 'desde ' + hhmm(c.act.a) + ' · ' + dur(AHORA_MIN - c.act.a)
        : (c.tr.length ? 'Terminó a las ' + hhmm(c.act.a) : 'Sin marcar hoy');
      var alerta = (c.e === 'afk' && AHORA_MIN - c.act.a >= 15) || (c.e === 'lunch' && AHORA_MIN - c.act.a >= 60);
      return '<article class="sac-eq-card is-' + c.e + (alerta ? ' is-alert' : '') + '">' +
        '<header>' + avatar(c.s, c.e) +
          '<div class="sac-eq-who"><span class="sac-cli-name">' + esc(c.s.nombre) + '</span><span class="sac-cli-sub">' + esc(c.s.username) + '</span></div>' +
          tagEstado(c.e) + '</header>' +
        '<p class="sac-eq-since">' + (alerta ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4M12 17h.01"></path></svg>' : '') + desde + '</p>' +
        '<div class="sac-eq-tl" role="img" aria-label="Línea de tiempo de hoy de ' + esc(c.s.nombre) + '">' +
          '<div class="sac-eq-track">' + seg + '<b class="sac-eq-now" style="left:' + ahoraPos + '%"></b></div>' +
          '<div class="sac-eq-ticks">' + ticks + '</div></div>' +
        '<dl class="sac-eq-stats">' +
          '<div><dt>Efectivo</dt><dd>' + (ef == null ? '—' : ef + '%') + '</dd></div>' +
          '<div><dt>Procesados</dt><dd>' + c.ped + '</dd></div>' +
          '<div><dt>Resp. prom.</dt><dd>' + (c.ped ? Math.round(c.resp) + ' min' : '—') + '</dd></div>' +
        '</dl></article>';
    }).join('');

    document.getElementById('sacEqLiveTxt').textContent = FUERA_HORARIO
      ? 'Fuera de horario · se simula el día a las ' + hhmm(AHORA_MIN)
      : 'Hoy · actualizado ' + hhmm(AHORA_MIN);
  }

  // =====================================================================
  // 2) BITÁCORA
  // =====================================================================
  var dtBit = null, filasBit = [];
  function datosBitacora() {
    var out = [];
    diasDelRango().forEach(function (d) {
      sacsFiltrados().forEach(function (s) {
        var tr = dia(s, d, corteDe(d)).tramos, prev = 'off';
        tr.forEach(function (x) {
          out.push({ d: d, min: x.a, s: s, de: prev, a: x.e, dur: x.e === 'off' ? null : x.b - x.a, abierto: x.abierto });
          prev = x.e;
        });
      });
    });
    out.sort(function (a, b) { return b.d - a.d || b.min - a.min; });
    return out;
  }
  function pintarBitacora() {
    filasBit = datosBitacora();
    var filas = filasBit.map(function (r) {
      return [
        '<span data-order="' + clave(r.d) + '">' + fechaCorta(r.d) + '</span>',
        '<span class="sac-num" data-order="' + pad(Math.floor(r.min)) + '">' + hhmm(r.min) + '</span>',
        nombreSac(r.s),
        '<span class="sac-eq-chg">' + tagEstado(r.de) + '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-label="a"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>' + tagEstado(r.a) + '</span>',
        r.dur == null ? '<span class="sac-muted">—</span>' : '<span class="sac-num' + ((r.a === 'afk' && r.dur >= 15) ? ' sac-eq-warn' : '') + '">' + dur(r.dur) + (r.abierto ? ' <small class="sac-muted">en curso</small>' : '') + '</span>',
        '<span class="sac-tag sac-tag-neutral">Manual</span>'
      ];
    });
    if (dtBit) { dtBit.clear().rows.add(filas).draw(); return; }
    dtBit = $('#sacEqTblBit').DataTable({
      data: filas, deferRender: true, pageLength: 15, lengthMenu: [15, 30, 50, 100], order: [[0, 'desc'], [1, 'desc']],
      columnDefs: [{ targets: [4], className: 'text-end' }, { targets: [3, 5], orderable: false }],
      language: lenguaje('cambios de estado'),
      dom: '<"sac-dt-top"f l>rt<"sac-dt-foot"ip>'
    });
  }

  // =====================================================================
  // 3) RESUMEN DEL EQUIPO + 5) POR SAC (comparten el cálculo)
  // =====================================================================
  function agregados() {
    var porSac = sacsFiltrados().map(function (s) {
      var a = { s: s, on: 0, afk: 0, lunch: 0, cambios: 0, afkLargos: 0, proc: 0, anul: 0, monto: 0, resp: 0, dias: 0 };
      diasDelRango().forEach(function (d) {
        var x = dia(s, d, corteDe(d)), t = totalesDia(x.tramos);
        if (x.tramos.length) a.dias++;
        a.on += t.on; a.afk += t.afk; a.lunch += t.lunch; a.cambios += t.cambios;
        x.tramos.forEach(function (tr) { if (tr.e === 'afk' && tr.b - tr.a >= 15) a.afkLargos++; });
        x.pedidos.forEach(function (p) { if (p.anulado) a.anul++; else { a.proc++; a.monto += p.monto; a.resp += p.resp; } });
      });
      a.base = a.on + a.afk + a.lunch; a.ef = pct(a.on, a.base); a.respProm = a.proc ? a.resp / a.proc : 0;
      a.porHora = a.on ? a.proc / (a.on / 60) : 0;
      return a;
    });
    var tot = porSac.reduce(function (t, a) {
      ['on', 'afk', 'lunch', 'cambios', 'afkLargos', 'proc', 'anul', 'monto', 'resp', 'base'].forEach(function (k) { t[k] += a[k]; });
      return t;
    }, { on: 0, afk: 0, lunch: 0, cambios: 0, afkLargos: 0, proc: 0, anul: 0, monto: 0, resp: 0, base: 0 });
    tot.ef = pct(tot.on, tot.base); tot.respProm = tot.proc ? tot.resp / tot.proc : 0; tot.porHora = tot.on ? tot.proc / (tot.on / 60) : 0;
    return { porSac: porSac, tot: tot };
  }

  function pintarResumen() {
    var g = agregados(), t = g.tot;
    document.getElementById('sacEqResKpis').innerHTML =
      kpi('Tiempo efectivo del equipo', t.ef == null ? '—' : t.ef + '%', textoPeriodo(), '<div class="sac-bar"><span style="width:' + (t.ef || 0) + '%"></span></div>') +
      kpi('<i class="sac-est-dot is-on" aria-hidden="true"></i> En línea', horas(t.on), 'Suma del equipo') +
      kpi('<i class="sac-est-dot is-afk" aria-hidden="true"></i> AFK', horas(t.afk), t.afkLargos + ' pausas de 15 min o más') +
      kpi('<i class="sac-est-dot is-lunch" aria-hidden="true"></i> Almuerzo', horas(t.lunch), 'Suma del equipo') +
      kpi('Cambios de estado', t.cambios.toLocaleString('es-NI'), 'Registrados manualmente');

    var filas = g.porSac.slice().sort(function (a, b) { return (b.ef || -1) - (a.ef || -1); });
    document.getElementById('sacEqResBody').innerHTML = filas.length ? filas.map(function (a) {
      var bar = a.base ? ['on', 'afk', 'lunch'].map(function (k) {
        if (!a[k]) return '';
        var p = a[k] * 100 / a.base;
        return '<i class="is-' + k + '" style="flex-grow:' + p.toFixed(3) + '" data-tip="' + esc('<strong>' + NOMBRE[k] + '</strong><span>' + esc(a.s.nombre) + '</span><span>' + horas(a[k]) + ' · ' + Math.round(p) + '%</span>') + '"></i>';
      }).join('') : '';
      return '<tr><td>' + nombreSac(a.s) + '</td>' +
        '<td>' + (a.base ? '<div class="sac-stack sac-stack-lg">' + bar + '</div>' : '<span class="sac-muted">Sin registros</span>') + '</td>' +
        '<td class="text-end sac-num">' + horas(a.on) + '</td>' +
        '<td class="text-end sac-num">' + horas(a.afk) + '</td>' +
        '<td class="text-end sac-num">' + horas(a.lunch) + '</td>' +
        '<td class="text-end"><strong class="sac-num sac-eq-ef">' + (a.ef == null ? '—' : a.ef + '%') + '</strong></td></tr>';
    }).join('') : '<tr><td colspan="6"><p class="sac-empty">No hay registros en este periodo.</p></td></tr>';
  }

  function pintarPorSac() {
    var g = agregados(), t = g.tot;
    var max = Math.max.apply(null, g.porSac.map(function (a) { return a.proc; }).concat([1]));
    var filas = g.porSac.slice().sort(function (a, b) { return b.proc - a.proc; });
    document.getElementById('sacEqPorSacBody').innerHTML = filas.length ? filas.map(function (a) {
      var lento = a.respProm >= 30;
      return '<tr><td>' + nombreSac(a.s) + '</td>' +
        '<td><div class="sac-eq-hbar" data-tip="' + esc('<strong>' + esc(a.s.nombre) + '</strong><span>' + a.proc + ' pedidos procesados</span>') + '"><span class="sac-num">' + a.proc + '</span><div class="sac-bar"><span style="width:' + (a.proc * 100 / max).toFixed(1) + '%"></span></div></div></td>' +
        '<td class="text-end sac-num">' + (a.anul ? a.anul : '<span class="sac-muted">0</span>') + '</td>' +
        '<td class="text-end sac-num">' + money(a.monto) + '</td>' +
        '<td class="text-end">' + (a.proc ? '<span class="sac-tag ' + (lento ? 'sac-tag-warn' : 'sac-tag-neutral') + '">' + Math.round(a.respProm) + ' min</span>' : '—') + '</td>' +
        '<td class="text-end sac-num">' + a.porHora.toLocaleString('es-NI', { maximumFractionDigits: 1, minimumFractionDigits: 1 }) + '</td>' +
        '<td class="text-end sac-num">' + horas(a.on) + '</td>' +
        '<td class="text-end"><strong class="sac-num sac-eq-ef">' + (a.ef == null ? '—' : a.ef + '%') + '</strong></td></tr>';
    }).join('') : '<tr><td colspan="8"><p class="sac-empty">No hay registros en este periodo.</p></td></tr>';
    document.getElementById('sacEqPorSacFoot').innerHTML = filas.length > 1 ?
      '<tr><th>Equipo · ' + filas.length + ' SAC</th><th class="sac-num">' + t.proc.toLocaleString('es-NI') + '</th><th class="text-end sac-num">' + t.anul + '</th>' +
      '<th class="text-end sac-num">' + money(t.monto) + '</th><th class="text-end sac-num">' + Math.round(t.respProm) + ' min</th>' +
      '<th class="text-end sac-num">' + t.porHora.toLocaleString('es-NI', { maximumFractionDigits: 1, minimumFractionDigits: 1 }) + '</th>' +
      '<th class="text-end sac-num">' + horas(t.on) + '</th><th class="text-end sac-num">' + (t.ef == null ? '—' : t.ef + '%') + '</th></tr>' : '';
  }

  // =====================================================================
  // 4) PEDIDOS PROCESADOS
  // =====================================================================
  var dtPed = null, filasPed = [];
  function pintarPedidos() {
    filasPed = [];
    diasDelRango().forEach(function (d) { sacsFiltrados().forEach(function (s) { filasPed = filasPed.concat(dia(s, d, corteDe(d)).pedidos); }); });
    filasPed.sort(function (a, b) { return b.fecha - a.fecha || b.procesado - a.procesado; });
    var ok = filasPed.filter(function (p) { return !p.anulado; });
    var monto = ok.reduce(function (a, p) { return a + p.monto; }, 0);
    var resp = ok.length ? ok.reduce(function (a, p) { return a + p.resp; }, 0) / ok.length : 0;
    var lentos = ok.filter(function (p) { return p.resp > 45; }).length;
    document.getElementById('sacEqPedKpis').innerHTML =
      kpi('Pedidos procesados', ok.length.toLocaleString('es-NI'), textoPeriodo()) +
      kpi('Anulados', (filasPed.length - ok.length).toLocaleString('es-NI'), filasPed.length ? Math.round((filasPed.length - ok.length) * 100 / filasPed.length) + '% del total' : '—') +
      kpi('Monto procesado', moneyK(monto), ok.length ? 'Ticket promedio ' + money(monto / ok.length) : '—') +
      kpi('Tiempo de respuesta prom.', ok.length ? Math.round(resp) + ' min' : '—', lentos + ' pedidos tardaron más de 45 min');

    var filas = filasPed.map(function (p) {
      return [
        '<span class="sac-num fw-semi-bold">' + p.id + '</span>',
        '<span data-order="' + clave(p.fecha) + '">' + fechaCorta(p.fecha) + '</span>',
        esc(p.cliente),
        nombreSac(p.sac),
        '<span class="sac-num">' + hhmm(p.recibido) + '</span>',
        '<span class="sac-num" data-order="' + clave(p.fecha) + pad(Math.floor(p.procesado)) + '">' + hhmm(p.procesado) + '</span>',
        '<span data-order="' + p.resp + '" class="sac-tag ' + (p.resp > 45 ? 'sac-tag-warn' : 'sac-tag-neutral') + '">' + p.resp + ' min</span>',
        '<span class="sac-num" data-order="' + p.monto + '">' + money(p.monto) + '</span>',
        p.anulado ? '<span class="sac-tag sac-tag-bad">Anulado</span>' : '<span class="sac-tag sac-tag-ok">Procesado</span>'
      ];
    });
    if (dtPed) { dtPed.clear().rows.add(filas).draw(); return; }
    dtPed = $('#sacEqTblPed').DataTable({
      data: filas, deferRender: true, pageLength: 15, lengthMenu: [15, 30, 50, 100], order: [[5, 'desc']],
      columnDefs: [{ targets: [6, 7], className: 'text-end' }],
      language: lenguaje('pedidos'),
      dom: '<"sac-dt-top"f l>rt<"sac-dt-foot"ip>'
    });
  }

  function lenguaje(que) {
    return {
      search: '', searchPlaceholder: 'Buscar…', lengthMenu: '_MENU_ filas',
      info: '_START_–_END_ de _TOTAL_ ' + que, infoEmpty: 'Sin ' + que, infoFiltered: '(de _MAX_)',
      zeroRecords: 'No hay resultados que coincidan', emptyTable: 'No hay ' + que + ' en este periodo',
      paginate: { previous: '‹', next: '›', first: '«', last: '»' }
    };
  }
  function textoPeriodo() {
    if (mismoDia(F.desde, F.hasta)) return mismoDia(F.desde, HOY) ? 'Hoy, hasta las ' + hhmm(AHORA_MIN) : fechaCorta(F.desde);
    return fechaCorta(F.desde) + ' – ' + fechaCorta(F.hasta);
  }

  // =====================================================================
  // Pestañas, filtros y exportación
  // =====================================================================
  var PINTAR = { vivo: pintarVivo, bitacora: pintarBitacora, resumen: pintarResumen, pedidos: pintarPedidos, porsac: pintarPorSac };
  function refrescar() { PINTAR[F.tab](); }

  document.querySelectorAll('[data-eq-tab]').forEach(function (b) {
    b.addEventListener('click', function () {
      F.tab = b.getAttribute('data-eq-tab');
      document.querySelectorAll('[data-eq-tab]').forEach(function (x) { var on = x === b; x.classList.toggle('active', on); x.setAttribute('aria-selected', on ? 'true' : 'false'); });
      document.querySelectorAll('[data-eq-pane]').forEach(function (p) { p.hidden = p.getAttribute('data-eq-pane') !== F.tab; });
      document.querySelectorAll('[data-eq-oculto]').forEach(function (el) { el.hidden = el.getAttribute('data-eq-oculto') === F.tab; });
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
  sel.addEventListener('change', function () { F.sac = sel.value; refrescar(); });

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

  // Exportar la pestaña actual a CSV (Excel lo abre directamente)
  document.getElementById('sacEqExport').addEventListener('click', function () {
    var filas = [], g;
    if (F.tab === 'vivo') {
      filas.push(['SAC', 'Usuario', 'Estado', 'Desde', 'Minutos en el estado']);
      SACS.forEach(function (s) { var tr = dia(s, HOY, AHORA_MIN).tramos, x = tr[tr.length - 1]; filas.push([s.nombre, s.username, x ? NOMBRE[x.e] : NOMBRE.off, x ? hhmm(x.a) : '', x && x.e !== 'off' ? Math.round(AHORA_MIN - x.a) : '']); });
    } else if (F.tab === 'bitacora') {
      filas.push(['Fecha', 'Hora', 'SAC', 'De', 'A', 'Duración (min)', 'Origen']);
      filasBit.forEach(function (r) { filas.push([clave(r.d), hhmm(r.min), r.s.nombre, NOMBRE[r.de], NOMBRE[r.a], r.dur == null ? '' : Math.round(r.dur), 'Manual']); });
    } else if (F.tab === 'pedidos') {
      filas.push(['Pedido', 'Fecha', 'Cliente', 'SAC', 'Recibido', 'Procesado', 'Respuesta (min)', 'Monto', 'Estado']);
      filasPed.forEach(function (p) { filas.push([p.id, clave(p.fecha), p.cliente, p.sac.nombre, hhmm(p.recibido), hhmm(p.procesado), p.resp, p.monto.toFixed(2), p.anulado ? 'Anulado' : 'Procesado']); });
    } else {
      g = agregados();
      filas.push(['SAC', 'En línea (h)', 'AFK (h)', 'Almuerzo (h)', 'Efectivo %', 'Procesados', 'Anulados', 'Monto', 'Resp. promedio (min)', 'Pedidos por hora en línea']);
      g.porSac.forEach(function (a) { filas.push([a.s.nombre, (a.on / 60).toFixed(2), (a.afk / 60).toFixed(2), (a.lunch / 60).toFixed(2), a.ef == null ? '' : a.ef, a.proc, a.anul, a.monto.toFixed(2), Math.round(a.respProm), a.porHora.toFixed(2)]); });
    }
    var csv = '﻿' + filas.map(function (f) { return f.map(function (v) { v = String(v); return /[",;\n]/.test(v) ? '"' + v.replace(/"/g, '""') + '"' : v; }).join(','); }).join('\r\n');
    var url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
    var a = document.createElement('a');
    a.href = url; a.download = 'equipo-sac-' + F.tab + '-' + clave(F.desde) + (mismoDia(F.desde, F.hasta) ? '' : '_' + clave(F.hasta)) + '.csv';
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
  });

  // Pestaña inicial (permite enlazar #bitacora, #resumen, etc.)
  var inicial = (location.hash || '').replace('#', '');
  var btnIni = document.querySelector('[data-eq-tab="' + (PINTAR[inicial] ? inicial : 'vivo') + '"]');
  btnIni.click();

  // El panel en vivo se refresca solo cada minuto
  setInterval(function () {
    if (F.tab !== 'vivo' || FUERA_HORARIO) return;
    var d = new Date(); AHORA_MIN = d.getHours() * 60 + d.getMinutes(); cache = {}; pintarVivo();
  }, 60000);
})();
</script>
