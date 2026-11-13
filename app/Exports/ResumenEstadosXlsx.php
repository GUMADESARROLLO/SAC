<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Excel corporativo del Resumen de Equipo SAC: misma estructura que la pantalla
 * (un SAC por bloque, un día por columna, tiempo por estado en HH:MM:SS y total del periodo)
 * más una hoja "Detalle" con una fila por SAC y día para filtrar o hacer tablas dinámicas.
 */
class ResumenEstadosXlsx
{
    // Paleta Unimark (la misma de sac-ui.css)
    const AZUL = '2B3B9E', NAVY = '15204F', TINTA = '26336B', GRIS = '4A5878', BORDE = 'D3DBE8', SUAVE = 'F2F5FA', ZEBRA = 'F7F9FC', HOY = 'E9EDFA';
    const COLOR = ['on' => '1E9E57', 'afk' => 'D98A00', 'lunch' => '3446B3'];
    const NOMBRE = ['on' => 'En línea', 'afk' => 'AFK', 'lunch' => 'Descanso'];
    const DIAS = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    const MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

    private $equipo, $min, $dias, $desde, $hasta, $generadoPor;

    /**
     * @param array  $equipo  [['id','nombre','username'], ...]
     * @param array  $min     EstadoSac::minutosPorDia()
     */
    public function __construct($equipo, array $min, $desde, $hasta, $generadoPor = '')
    {
        $this->equipo = $equipo;
        $this->min = $min;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->generadoPor = $generadoPor;
        $this->dias = [];
        for ($d = strtotime($desde); $d <= strtotime($hasta); $d = strtotime('+1 day', $d)) $this->dias[] = date('Y-m-d', $d);
    }

    private function fechaLarga($f) { $t = strtotime($f); return date('j', $t) . ' ' . self::MESES[date('n', $t) - 1] . ' ' . date('Y', $t); }
    public function periodo() { return $this->desde === $this->hasta ? $this->fechaLarga($this->desde) : $this->fechaLarga($this->desde) . ' – ' . $this->fechaLarga($this->hasta); }
    private function esFds($f) { $w = (int) date('w', strtotime($f)); return $w === 0 || $w === 6; }
    private static function col($n) { return Coordinate::stringFromColumnIndex($n); }

    public function libro()
    {
        $libro = new Spreadsheet();
        $libro->getProperties()->setCreator('SAC Unimark')->setTitle('Equipo SAC · Tiempo por estado')
            ->setSubject('Resumen ' . $this->periodo())->setCompany('Unimark S.A.');
        $libro->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10)->getColor()->setRGB(self::NAVY);

        $this->hojaResumen($libro->getActiveSheet());
        $this->hojaDetalle($libro->createSheet());
        $libro->setActiveSheetIndex(0);
        return $libro;
    }

    private function hojaResumen($h)
    {
        $h->setTitle('Resumen');
        $h->setShowGridlines(false);
        $nDias = count($this->dias);
        $cIni = 4;                              // D: primer día
        $cTot = $cIni + $nDias;                 // columna Total
        $ultima = self::col($cTot);

        // ---- Encabezado del documento ----
        $logo = public_path('images/logo-20.png');
        if (is_file($logo)) {
            $dr = new Drawing();
            $dr->setPath($logo)->setHeight(46)->setCoordinates('A1')->setOffsetX(4)->setOffsetY(4)->setWorksheet($h);
        }
        $h->getRowDimension(1)->setRowHeight(24);
        $h->getRowDimension(2)->setRowHeight(20);
        $h->getRowDimension(3)->setRowHeight(16);
        $h->setCellValue('C1', 'Equipo SAC · Tiempo por estado');
        $h->getStyle('C1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB(self::NAVY);
        $h->setCellValue('C2', 'Periodo: ' . $this->periodo());
        $h->getStyle('C2')->getFont()->setSize(11)->getColor()->setRGB(self::TINTA);
        $h->setCellValue('C3', 'Generado el ' . date('d/m/Y \a \l\a\s H:i') . ($this->generadoPor ? ' · ' . $this->generadoPor : ''));
        $h->getStyle('C3')->getFont()->setSize(9)->getColor()->setRGB(self::GRIS);
        $h->getStyle('A4:' . $ultima . '4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::AZUL);
        $h->getRowDimension(4)->setRowHeight(6);

        // ---- Encabezado de la tabla (fila 6) ----
        $f = 6;
        $h->fromArray(['Colaborador', 'Usuario', 'Estado'], null, 'A' . $f);
        $hoy = date('Y-m-d');
        foreach ($this->dias as $i => $dia) {
            $t = strtotime($dia);
            $h->setCellValue(self::col($cIni + $i) . $f, self::DIAS[(int) date('w', $t)] . ' ' . date('d/m', $t) . ($dia === $hoy ? "\nHoy" : ''));
        }
        $h->setCellValue($ultima . $f, "Total\ndel periodo");
        $enc = $h->getStyle('A' . $f . ':' . $ultima . $f);
        $enc->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('FFFFFF');
        $enc->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::AZUL);
        $enc->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $enc->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::AZUL);
        $h->getStyle('A' . $f . ':C' . $f)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
        foreach ($this->dias as $i => $dia) {
            if ($this->esFds($dia)) $h->getStyle(self::col($cIni + $i) . $f)->getFill()->getStartColor()->setRGB('5A6690');
        }
        $h->getStyle($ultima . $f)->getFill()->getStartColor()->setRGB(self::NAVY);
        $h->getRowDimension($f)->setRowHeight(30);

        // ---- Un bloque de 4 filas por SAC: En línea, AFK, Descanso, Efectivo ----
        $f = 7;
        $estados = ['on', 'afk', 'lunch'];
        foreach (array_values($this->equipo instanceof \Illuminate\Support\Collection ? $this->equipo->all() : $this->equipo) as $n => $s) {
            $f0 = $f;
            $porDia = $this->min[$s['id']] ?? [];
            $h->setCellValue('A' . $f0, $s['nombre']);
            $h->setCellValue('B' . $f0, $s['username']);
            $h->mergeCells('A' . $f0 . ':A' . ($f0 + 3));
            $h->mergeCells('B' . $f0 . ':B' . ($f0 + 3));

            foreach ($estados as $j => $e) {
                $fila = $f0 + $j;
                $h->setCellValue('C' . $fila, '● ' . self::NOMBRE[$e]);
                $h->getStyle('C' . $fila)->getFont()->getColor()->setRGB(self::COLOR[$e]);
                $h->getStyle('C' . $fila)->getFont()->setBold(true);
            }
            $h->setCellValue('C' . ($f0 + 3), 'Efectivo');
            $h->getStyle('C' . ($f0 + 3))->getFont()->setItalic(true)->getColor()->setRGB(self::GRIS);

            $tot = ['on' => 0, 'afk' => 0, 'lunch' => 0]; $conDatos = 0;
            foreach ($this->dias as $i => $dia) {
                $c = self::col($cIni + $i);
                $m = $porDia[$dia] ?? null;
                if ($dia > $hoy) continue;                         // días que aún no pasan: vacíos
                if (!$m) {
                    if (!$this->esFds($dia)) {
                        $h->setCellValue($c . $f0, 'Sin registro');
                        $h->mergeCells($c . $f0 . ':' . $c . ($f0 + 3));
                        $h->getStyle($c . $f0)->getFont()->setItalic(true)->getColor()->setRGB('7F8AA6');
                    }
                    continue;
                }
                $conDatos++;
                foreach ($estados as $j => $e) {
                    $tot[$e] += $m[$e];
                    $h->setCellValue($c . ($f0 + $j), $m[$e] / 1440);   // fracción de día => formato [hh]:mm:ss
                    if ($m[$e] < 1 / 60) $h->getStyle($c . ($f0 + $j))->getFont()->getColor()->setRGB('B5BED0');
                }
                $base = $m['on'] + $m['afk'] + $m['lunch'];
                $h->setCellValue($c . ($f0 + 3), $base > 0 ? $m['on'] / $base : null);
                if ($m['curso']) $h->getComment($c . $f0)->getText()->createTextRun('En curso: suma hasta la hora de generación.');
            }
            // Total del periodo
            foreach ($estados as $j => $e) $h->setCellValue($ultima . ($f0 + $j), $conDatos ? $tot[$e] / 1440 : null);
            $base = $tot['on'] + $tot['afk'] + $tot['lunch'];
            if ($conDatos) $h->setCellValue($ultima . ($f0 + 3), $base > 0 ? $tot['on'] / $base : null);
            else { $h->setCellValue($ultima . $f0, 'Sin registro'); $h->mergeCells($ultima . $f0 . ':' . $ultima . ($f0 + 3)); $h->getStyle($ultima . $f0)->getFont()->setItalic(true)->getColor()->setRGB('7F8AA6'); }

            // Formato del bloque
            $rango = 'A' . $f0 . ':' . $ultima . ($f0 + 3);
            if ($n % 2 === 1) $h->getStyle($rango)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::ZEBRA);
            $h->getStyle(self::col($cIni) . $f0 . ':' . $ultima . ($f0 + 2))->getNumberFormat()->setFormatCode('[hh]:mm:ss');
            $h->getStyle(self::col($cIni) . ($f0 + 3) . ':' . $ultima . ($f0 + 3))->getNumberFormat()->setFormatCode('0%');
            $h->getStyle(self::col($cIni) . ($f0 + 3) . ':' . $ultima . ($f0 + 3))->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB(self::GRIS);
            $h->getStyle(self::col($cIni) . $f0 . ':' . $ultima . ($f0 + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $h->getStyle('A' . $f0 . ':B' . ($f0 + 3))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);
            $h->getStyle('A' . $f0)->getFont()->setBold(true)->setSize(11);
            $h->getStyle('B' . $f0)->getFont()->getColor()->setRGB(self::GRIS);
            $h->getStyle('C' . $f0 . ':C' . ($f0 + 3))->getAlignment()->setIndent(1);
            $h->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::BORDE);
            $h->getStyle('A' . ($f0 + 3) . ':' . $ultima . ($f0 + 3))->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('9AA6C2');
            // Fin de semana y hoy, sombreados como en pantalla
            foreach ($this->dias as $i => $dia) {
                $c = self::col($cIni + $i);
                if ($dia === $hoy) $h->getStyle($c . $f0 . ':' . $c . ($f0 + 3))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::HOY);
                elseif ($this->esFds($dia)) $h->getStyle($c . $f0 . ':' . $c . ($f0 + 3))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEF1F6');
            }
            // Columna total resaltada
            $h->getStyle($ultima . $f0 . ':' . $ultima . ($f0 + 2))->getFont()->setBold(true);
            $h->getStyle($ultima . $f0 . ':' . $ultima . ($f0 + 3))->getBorders()->getLeft()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::AZUL);
            for ($k = 0; $k < 4; $k++) $h->getRowDimension($f0 + $k)->setRowHeight(16);
            $f += 4;
        }
        if ($f === 7) {
            $h->setCellValue('A7', 'No hay usuarios SAC activos.');
            $f = 8;
        }

        // ---- Notas ----
        $f++;
        $notas = [
            'Tiempo en formato HH:MM:SS. Efectivo = En línea ÷ (En línea + AFK + Descanso). El tiempo Desconectado no se cuenta.',
            'El día de hoy suma hasta la hora de generación. Los días sin estados marcados aparecen como "Sin registro".',
            'La hoja "Detalle" tiene una fila por SAC y día para filtrar o hacer tablas dinámicas.',
        ];
        foreach ($notas as $nota) {
            $h->setCellValue('A' . $f, $nota);
            $h->mergeCells('A' . $f . ':' . $ultima . $f);
            $h->getStyle('A' . $f)->getFont()->setSize(9)->getColor()->setRGB(self::GRIS);
            $f++;
        }

        // ---- Anchos, inmovilizar e impresión ----
        $h->getColumnDimension('A')->setWidth(30);
        $h->getColumnDimension('B')->setWidth(12);
        $h->getColumnDimension('C')->setWidth(13);
        for ($c = $cIni; $c < $cTot; $c++) $h->getColumnDimension(self::col($c))->setWidth(11.5);
        $h->getColumnDimension($ultima)->setWidth(13);
        $h->freezePane('D7');
        $ps = $h->getPageSetup();
        $ps->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setPaperSize(PageSetup::PAPERSIZE_LETTER)
            ->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(6, 6);
        $h->getPageMargins()->setTop(0.5)->setBottom(0.6)->setLeft(0.4)->setRight(0.4);
        $h->getHeaderFooter()->setOddFooter('&L&8Unimark S.A. · Equipo SAC · ' . $this->periodo() . '&R&8Página &P de &N');
    }

    private function hojaDetalle($h)
    {
        $h->setTitle('Detalle');
        $cab = ['SAC', 'Usuario', 'Fecha', 'Día', 'En línea', 'AFK', 'Descanso', 'Total', 'Efectivo'];
        $h->fromArray($cab, null, 'A1');
        $enc = $h->getStyle('A1:I1');
        $enc->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $enc->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::AZUL);
        $enc->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $h->getRowDimension(1)->setRowHeight(22);
        $f = 2; $hoy = date('Y-m-d');
        foreach ($this->equipo as $s) {
            foreach ($this->dias as $dia) {
                if ($dia > $hoy) continue;
                $m = $this->min[$s['id']][$dia] ?? ['on' => 0, 'afk' => 0, 'lunch' => 0];
                $base = $m['on'] + $m['afk'] + $m['lunch'];
                $t = strtotime($dia);
                $h->fromArray([$s['nombre'], $s['username'], \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($t), self::DIAS[(int) date('w', $t)],
                    $m['on'] / 1440, $m['afk'] / 1440, $m['lunch'] / 1440, $base / 1440, $base > 0 ? $m['on'] / $base : null], null, 'A' . $f);
                $f++;
            }
        }
        if ($f > 2) {
            $h->getStyle('C2:C' . ($f - 1))->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $h->getStyle('E2:H' . ($f - 1))->getNumberFormat()->setFormatCode('[hh]:mm:ss');
            $h->getStyle('I2:I' . ($f - 1))->getNumberFormat()->setFormatCode('0%');
            $h->getStyle('A1:I' . ($f - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_HAIR)->getColor()->setRGB(self::BORDE);
            $h->setAutoFilter('A1:I' . ($f - 1));
        }
        foreach (['A' => 30, 'B' => 12, 'C' => 12, 'D' => 7, 'E' => 11, 'F' => 11, 'G' => 11, 'H' => 11, 'I' => 10] as $c => $w) $h->getColumnDimension($c)->setWidth($w);
        $h->freezePane('A2');
    }
}
