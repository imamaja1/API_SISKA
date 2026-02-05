@extends('layouts.app')

@section('title', 'OBE Auth Tester')

@section('content')
@php
$serverCookieNames = array_keys(request()->cookies->all());
@endphp

<style>
    body {
        background-color: #f5f5f5;
    }

    .postman-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .postman-header {
        background: white;
        border-radius: 8px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .postman-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #212529;
        margin: 0 0 8px 0;
    }

    .postman-header .subtitle {
        color: #6c757d;
        font-size: 14px;
        margin: 0;
    }

    .postman-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .postman-card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 16px 24px;
        font-weight: 600;
        font-size: 14px;
        color: #495057;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .postman-card-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #495057;
        margin-bottom: 10px;
        display: block;
    }

    .label-hint {
        font-weight: 400;
        color: #6c757d;
        margin-left: 6px;
    }

    .postman-input {
        border: 1px solid #dfe3e6;
        border-radius: 4px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s;
        width: 100%;
    }

    .postman-input:focus {
        outline: none;
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .postman-textarea {
        border: 1px solid #dfe3e6;
        border-radius: 4px;
        padding: 14px;
        font-size: 13px;
        font-family: 'Consolas', 'Monaco', monospace;
        background: #2d2d2d;
        color: #f8f8f2;
        transition: all 0.2s;
        resize: vertical;
        width: 100%;
        line-height: 1.6;
    }

    .postman-textarea:focus {
        outline: none;
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .method-select {
        background: white;
        border: 1px solid #dfe3e6;
        border-radius: 4px 0 0 4px;
        padding: 10px 14px;
        font-size: 14px;
        font-weight: 600;
        color: #495057;
        cursor: pointer;
        max-width: 130px;
    }

    .method-select:focus {
        outline: none;
        border-color: #4a90e2;
    }

    .url-input {
        border-left: none;
        border-radius: 0 4px 4px 0 !important;
    }

    .btn-postman {
        border: 1px solid #dfe3e6;
        border-radius: 4px;
        padding: 9px 20px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-postman:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-send {
        background: #4a90e2;
        color: white;
        border-color: #4a90e2;
    }

    .btn-send:hover {
        background: #357abd;
        border-color: #357abd;
        color: white;
    }

    .btn-auto {
        background: white;
        color: #495057;
    }

    .btn-auto:hover {
        background: #f8f9fa;
    }

    .btn-preset {
        background: white;
        border: 1px solid #dfe3e6;
        border-radius: 4px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        color: #495057;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-preset:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-success {
        background: #d4edda;
        color: #155724;
    }

    .status-error {
        background: #f8d7da;
        color: #721c24;
    }

    .status-loading {
        background: #fff3cd;
        color: #856404;
    }

    .status-default {
        background: #e9ecef;
        color: #6c757d;
    }

    .code-block {
        background: #2d2d2d;
        color: #f8f8f2;
        border-radius: 4px;
        padding: 16px;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 12px;
        line-height: 1.6;
        overflow: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .debug-info {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 20px;
    }

    .debug-row {
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e9ecef;
    }

    .debug-row:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .debug-label {
        font-weight: 600;
        font-size: 13px;
        color: #495057;
        margin-bottom: 6px;
    }

    .debug-value {
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 12px;
        color: #212529;
    }

    .response-meta {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .meta-label {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
    }

    .meta-value {
        font-size: 13px;
        color: #212529;
        font-weight: 600;
    }
</style>

<div class="postman-container">
    <div class="postman-header">
        <h1>OBE API Tester</h1>
        <p class="subtitle">Sanctum SPA Cookie Authentication - Testing login/me/logout endpoints</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="postman-card">
                <div class="postman-card-body">
                    <div class="form-group">
                        <label class="form-label">Method & URL</label>
                        <div class="input-group">
                            <select id="method" class="method-select">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                            <button class="btn-postman btn-send" id="btnSend">Send Request</button>
                            <input type="text" id="url" class="postman-input url-input" placeholder="/api/v1/obe/login"
                                value="/sanctum/csrf-cookie">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Headers
                            <span class="label-hint">(JSON format)</span>
                        </label>
                        <textarea id="headers" class="postman-textarea" rows="4">{}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Body / Param (for GET requests)
                            <span class="label-hint">(JSON format)</span>
                        </label>
                        <textarea id="body" class="postman-textarea" rows="8">{}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Quick Presets</label>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button class="btn-preset" onclick="loadPreset('csrf')">CSRF Cookie</button>
                            <button class="btn-preset" onclick="loadPreset('login')">Login</button>
                            <button class="btn-preset" onclick="loadPreset('me')">Test</button>
                            <button class="btn-preset" onclick="loadPreset('logout')">Logout</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="postman-card">
                <div class="postman-card-header">
                    Debug Information
                </div>
                <div class="postman-card-body">
                    <div class="debug-info">
                        <div class="debug-row">
                            <div class="debug-label">Server Host</div>
                            <div class="debug-value">{{ request()->getHost() }}</div>
                        </div>
                        <div class="debug-row">
                            <div class="debug-label">Server Cookies</div>
                            <div class="debug-value">{{ implode(', ', $serverCookieNames) ?: '(none)' }}</div>
                        </div>
                        <div class="debug-row">
                            <div class="debug-label">Browser Cookies</div>
                            <div class="code-block" id="cookieBox" style="margin-top: 8px; max-height: 100px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="postman-card">
                <div class="postman-card-header">
                    Response
                </div>
                <div class="response-meta">
                    <div class="meta-item">
                        <span class="meta-label">Status:</span>
                        <span id="resStatus" class="status-badge status-default">-</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Time:</span>
                        <span id="resTime" class="meta-value">-</span>
                    </div>
                    <div style="margin-left: auto;">
                        <button class="btn-preset" id="btnClear">Clear</button>
                    </div>
                </div>
                <div class="postman-card-body">
                    <div class="form-group">
                        <label class="form-label">Response Headers</label>
                        <div id="resHeaders" class="code-block" style="min-height: 120px; max-height: 150px;">-</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Response Body</label>
                        <div id="resBody" class="code-block" style="min-height: 350px; max-height: 450px;">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const $method = document.getElementById('method');
    const $url = document.getElementById('url');
    const $headers = document.getElementById('headers');
    const $body = document.getElementById('body');
    const $resStatus = document.getElementById('resStatus');
    const $resTime = document.getElementById('resTime');
    const $resHeaders = document.getElementById('resHeaders');
    const $resBody = document.getElementById('resBody');
    const $cookieBox = document.getElementById('cookieBox');

    let startTime = 0;

    function refreshCookieBox() {
        $cookieBox.textContent = document.cookie || '(empty / all HttpOnly)';
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function getXsrfToken() {
        const raw = getCookie('XSRF-TOKEN');
        if (!raw) return null;
        try {
            return decodeURIComponent(raw);
        } catch (e) {
            return raw;
        }
    }

    function loadPreset(type) {
        const xsrf = getXsrfToken();
        
        switch(type) {
            case 'csrf':
                $method.value = 'GET';
                $url.value = '/sanctum/csrf-cookie';
                $headers.value = '{}';
                $body.value = '{}';
                break;
            case 'login':
                $method.value = 'POST';
                $url.value = '/api/v1/obe/login';
                $headers.value = JSON.stringify({
                    'Content-Type': 'application/json'
                }, null, 2);
                $body.value = JSON.stringify({
                    email: 'helna.wardhana@universitasbumigora.ac.id',
                    password: 'Azwar@93'
                }, null, 2);
                break;
            case 'me':
                $method.value = 'GET';
                $url.value = '/api/v1/obe/me';
                $headers.value = '{}';
                $body.value = '{}';
                break;
            case 'logout':
                $method.value = 'POST';
                $url.value = '/api/v1/obe/logout';
                $headers.value = JSON.stringify({
                    'Content-Type': 'application/json'
                }, null, 2);
                $body.value = '{}';
                break;
        }
    }

    async function sendRequest() {
        const method = $method.value;
        let url = $url.value;
        
        let headers = {};
        let body = null;

        try {
            headers = JSON.parse($headers.value || '{}');
        } catch (e) {
            alert('Headers must be valid JSON!');
            return;
        }

        // Auto-inject X-XSRF-TOKEN untuk POST/PUT/DELETE jika belum ada
        if (method !== 'GET' && !headers['X-XSRF-TOKEN']) {
            const xsrf = getXsrfToken();
            if (xsrf) {
                headers['X-XSRF-TOKEN'] = xsrf;
            }
        }

        try {
            const bodyText = $body.value.trim();
            const hasBody = bodyText && bodyText !== '{}';

            if (method === 'GET') {
                // GET tidak mengirim body. Jika user isi JSON di body, ubah jadi query param.
                if (hasBody) {
                    const payload = JSON.parse(bodyText);
                    const searchParams = new URLSearchParams();
                    Object.keys(payload || {}).forEach((key) => {
                        if (payload[key] !== undefined && payload[key] !== null) {
                            searchParams.set(key, String(payload[key]));
                        }
                    });

                    if ([...searchParams.keys()].length > 0) {
                        url = url.includes('?')
                            ? `${url}&${searchParams.toString()}`
                            : `${url}?${searchParams.toString()}`;
                    }
                }
            } else {
                if (hasBody) {
                    body = JSON.stringify(JSON.parse(bodyText));
                }
            }
        } catch (e) {
            alert('Body must be valid JSON!');
            return;
        }

        startTime = performance.now();
        $resStatus.textContent = 'Loading...';
        $resStatus.className = 'status-badge status-loading';
        $resTime.textContent = '-';
        $resHeaders.textContent = '-';
        $resBody.textContent = 'Sending request...';

        try {
            const res = await fetch(url, {
                method: method,
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    ...headers
                },
                body: body
            });

            const elapsed = (performance.now() - startTime).toFixed(0);
            $resTime.textContent = `${elapsed}ms`;

            // Status
            $resStatus.textContent = `${res.status} ${res.statusText}`;
            if (res.ok) {
                $resStatus.className = 'status-badge status-success';
            } else {
                $resStatus.className = 'status-badge status-error';
            }

            // Headers
            const resHeadersObj = {};
            res.headers.forEach((value, key) => {
                resHeadersObj[key] = value;
            });
            $resHeaders.textContent = JSON.stringify(resHeadersObj, null, 2);

            // Body
            const contentType = res.headers.get('content-type') || '';
            let resBody;
            if (contentType.includes('application/json')) {
                resBody = await res.json().catch(() => null);
                $resBody.textContent = JSON.stringify(resBody, null, 2);
            } else {
                resBody = await res.text().catch(() => '');
                $resBody.textContent = resBody || '(empty)';
            }

            refreshCookieBox();

        } catch (err) {
            const elapsed = (performance.now() - startTime).toFixed(0);
            $resTime.textContent = `${elapsed}ms`;
            $resStatus.textContent = 'ERROR';
            $resStatus.className = 'status-badge status-error';
            $resHeaders.textContent = '-';
            $resBody.textContent = `Error: ${err.message}`;
        }
    }

    async function runAutoTest() {
        const tests = [
            { name: 'csrf', delay: 500 },
            { name: 'login', delay: 800 },
            { name: 'me', delay: 800 },
            { name: 'logout', delay: 800 }
        ];

        for (const test of tests) {
            loadPreset(test.name);
            await new Promise(resolve => setTimeout(resolve, 300));
            await sendRequest();
            await new Promise(resolve => setTimeout(resolve, test.delay));
        }

        alert('✅ Auto test selesai! Cek response untuk setiap endpoint.');
    }

    document.getElementById('btnSend').addEventListener('click', sendRequest);
    document.getElementById('btnAutoTest').addEventListener('click', runAutoTest);
    document.getElementById('btnClear').addEventListener('click', () => {
        $resStatus.textContent = '-';
        $resStatus.className = 'status-badge status-default';
        $resTime.textContent = '-';
        $resHeaders.textContent = '-';
        $resBody.textContent = '-';
        refreshCookieBox();
    });

    // Initialize
    refreshCookieBox();
    setInterval(refreshCookieBox, 2000);
</script>
@endsection