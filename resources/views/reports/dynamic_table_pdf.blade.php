<!-- <!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
        .header { text-align: center; }
        img.logo { width: 100px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists($logo))
            <img src="https://astrotest.diploy.in/public/storage/images/AdminLogo1732085016.png" class="logo" alt="Logo">
        @endif
        <h3>{{ $title }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                @foreach(array_keys($tableData[0] ?? []) as $key)
                    <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($tableData as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
 -->