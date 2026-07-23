<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    body {
        font-family: Arial;
        margin: 40px;
    }

    h1 {
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td {
        border: 1px solid black;
        padding: 8px;
    }
</style>

<body>
    <h1>CONSTANCIA</h1>
    <p>Municipalidad de Guatemala</p>
    <table>
        <tr>
            <td>Nombre</td>
            <td>Juan Pérez</td>
        </tr>
        <tr>
            <td>DPI</td>
            <td>1234 56789 0101</td>
        </tr>
        <tr>
            <td>Dirección</td>
            <td>Zona 18</td>
        </tr>
        <tr>
            <td>Fecha</td>
            <td>{{ now()->format('d/m/Y') }}</td>
        </tr>

    </table>
</body>

</html>
