@extends('layouts.lyt_gumadesk')
    <table width="100%">
        <tr>
            <td valign="top"></td>
            <td align ="right">
                <h3>UNIMARK, S.A.</h3>
                    <pre>
                        Villa Fontana, Club Terraza 150m Oeste
                        Managua, Nicaragua
                        Tel.: 2278-8787 - E-mail : info@unimarksa.com
                        RUC: J0310000121249
                    </pre>
            </td>
        </tr>
    </table>
    <div class="text-center ml ">
        <strong>INVENTARIO TOTALIZADO</strong>
    </div>
    </br>
    <table width="100%">
        <thead style="background-color: lightgray;">
            <tr style="text-align: center;">
                <th style="border: 1px solid black; border-collapse: collapse;">Codigo</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Descripcion</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Cant. disponible 002</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Cant. disponible 005</th>
                <th style="border: 1px solid black; border-collapse: collapse;">Precio Farmacia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventario as $inv)
                <tr>
                    <td style="border: 1px solid black; border-collapse: collapse;">{{ $inv['ARTICULO'] }}</td>
                    <td style="border: 1px solid black; border-collapse: collapse;">{{ $inv['DESCRIPCION'] }}</td>
                    <td style="border: 1px solid black; border-collapse: collapse;">{{ number_format($inv['total'],2) }}</td>
                    <td style="border: 1px solid black; border-collapse: collapse;">{{ number_format($inv['005'],2) }}</td>
                    <td style="border: 1px solid black; border-collapse: collapse;">{{ number_format($inv['PRECIO_FARMACIA'],2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
