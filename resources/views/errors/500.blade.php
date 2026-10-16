<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error Diagnostic | SeTiket</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background-color: #0b0f19;
            color: #e2e8f0;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .container {
            max-width: 960px;
            margin: 0 auto;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .badge {
            background-color: #ef4444;
            color: white;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            display: inline-block;
        }
        h1 {
            color: #f87171;
            font-size: 18px;
            margin: 16px 0 8px;
            word-break: break-word;
        }
        .file-info {
            color: #94a3b8;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        pre {
            background: #0f172a;
            color: #f1f5f9;
            padding: 16px;
            border-radius: 10px;
            overflow-x: auto;
            font-size: 12px;
            line-height: 1.5;
            max-height: 500px;
            border: 1px solid #334155;
        }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 16px;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge">500 | DETAIL ERROR SISTEM</span>
        <h1>{{ isset($exception) ? $exception->getMessage() : 'Internal Server Error' }}</h1>
        <div class="file-info">
            <strong>Exception Type:</strong> {{ isset($exception) ? get_class($exception) : 'Unknown' }}<br>
            <strong>Lokasi File:</strong> {{ isset($exception) ? $exception->getFile() . ':' . $exception->getLine() : '-' }}
        </div>
        <strong>Stack Trace:</strong>
        <pre>{{ isset($exception) ? $exception->getTraceAsString() : 'Tidak ada trace.' }}</pre>
        <div>
            <a href="{{ url('/admin/dashboard') }}" class="btn">Muat Ulang Dashboard</a>
            <a href="{{ url('/') }}" class="btn" style="background: #475569;">Ke Beranda</a>
        </div>
    </div>
</body>
</html>
