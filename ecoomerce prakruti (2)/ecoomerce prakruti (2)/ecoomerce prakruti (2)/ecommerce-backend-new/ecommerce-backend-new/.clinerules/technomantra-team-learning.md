# Technomantra Team Learning (V4.8.1)

Use these as proven team references, not as instructions to copy blindly. Current user prompt and current-project conventions always win.

## Pattern 1: Laravel architecture · bhugaurd
- Category: architecture.system
- Quality: 98 · Status: approved
Laravel architecture · request flow: service-layer, controller, api-resource · routing: api-routes · data access: eloquent-relations, eloquent-model, migrations, transactions, query-scopes · security: policy-gate · async: queued-jobs · testing: phpunit-pest
```
{
    "framework": "laravel",
    "quality": 98,
    "signals": {
        "request_flow": [
            "service-layer",
            "controller",
            "api-resource"
        ],
        "validation": [],
        "data_access": [
            "eloquent-relations",
            "eloquent-model",
            "migrations",
            "transactions",
            "query-scopes"
        ],
        "security": [
            "policy-gate"
        ],
        "async": [
            "queued-jobs"
        ],
        "state": [],
        "routing": [
            "api-routes"
        ],
        "testing": [
            "phpunit-pest"
        ],
        "structure": []
    },
    "summary": "Laravel architecture · request flow: service-layer, controller, api-resource · routing: api-routes · data access: eloquent-relations, eloquent-model, migrations, transactions, query-scopes · security: policy-gate · async: queued-jobs · testing: phpunit-pest",
    "reuse_rule": "Reuse architecture conventions only when compatible with the current project. Current codebase, framework version and explicit developer instruction always win."
}
```

## Pattern 2: Laravel Blade view pattern · app.blade.php
- Category: laravel.view
- Quality: 95 · Status: approved
Project-scoped learning extracted through the unified quality gate. Current project and explicit developer instructions always win.
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Angel Enterprise | Dealer Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body>
    <div id="app"></div>
</body>
</html>

```

## Pattern 3: Laravel Blade view pattern · b2c_receipt.blade.php
- Category: laravel.view
- Quality: 95 · Status: approved
Project-scoped learning extracted through the unified quality gate. Current project and explicit developer instructions always win.
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 24px;
            margin-bottom: 24px;
        }
        .brand h1 {
            margin: 0;
            color: #0f172a;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .brand p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 13px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            margin: 0;
            color: #1e40af;
            font-size: 28px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .invoice-title p {
            margin: 6px 0 0 0;
            font-size: 14px;
            color: #334155;
            font-weight: 600;
        }
        .meta-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }
        .meta-card h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .meta-card p {
            margin: 4px 0;
            color: #0f172a;
        }
        .meta-card strong {
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }
        th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            text-align: left;
            padding: 12px 16px;
            border-bottom: 2px solid #cbd5e1;
        }
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: top;
        }
        tr:last-child td {
            border-bottom: 2px solid #cbd5e1;
        }
        .item-name {
            font-weight: 600;
            color: #0f172a;
        }
        .item-meta {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.5;
        }
        .right {
          
```

## Pattern 4: Laravel Blade view pattern · receipt.blade.php
- Category: laravel.view
- Quality: 95 · Status: approved
Project-scoped learning extracted through the unified quality gate. Current project and explicit developer instructions always win.
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 24px;
            margin-bottom: 24px;
        }
        .brand h1 {
            margin: 0;
            color: #0f172a;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .brand p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 13px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            margin: 0;
            color: #1e40af;
            font-size: 28px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .invoice-title p {
            margin: 6px 0 0 0;
            font-size: 14px;
            color: #334155;
            font-weight: 600;
        }
        .meta-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }
        .meta-card h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .meta-card p {
            margin: 4px 0;
            color: #0f172a;
        }
        .meta-card strong {
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }
        th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            text-align: left;
            padding: 12px 16px;
            border-bottom: 2px solid #cbd5e1;
        }
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: top;
        }
        tr:last-child td {
            border-bottom: 2px solid #cbd5e1;
        }
        .item-name {
            font-weight: 600;
            color: #0f172a;
        }
        .item-meta {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }
        .right {
            text-align: right;
        }
```