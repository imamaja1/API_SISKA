@extends('layouts.app')

@section('content')
    <div class="documentation-layout">
        <!-- Sidebar -->
        <aside class="doc-sidebar">
            <div class="sidebar-content">
                <h5 class="sidebar-title">API Reference</h5>
                <nav class="sidebar-nav">
                    @foreach ($docs as $doc)
                        <a href="#katagori{{ $doc->id }}" class="nav-link">{{ $doc->name }}</a>
                        @foreach ($doc->apiDocs as $item)
                            <a href="#endpoint{{ $item->id }}" class="nav-sublink">{{ $item->judul }}</a>
                        @endforeach
                    @endforeach
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="doc-main">
            <header class="doc-header">
                <h1>API SISKA Documentation</h1>
                <p class="doc-subtitle">Complete reference for API integration</p>
            </header>

            @foreach ($docs as $doc)
                <section class="doc-section" id="katagori{{ $doc->id }}">
                    <h2 class="section-title">{{ $doc->name }}</h2>
                    @if ($doc->apiDocs->count() > 0)
                        @foreach ($doc->apiDocs as $item)
                            <article class="api-endpoint row" id="endpoint{{ $item->id }}">

                                <div class="endpoint-header">
                                    <h3 class="endpoint-title">{{ $item->judul }}</h3>
                                    @if ($item->description)
                                        <p class="endpoint-description">{{ $item->description }}</p>
                                    @endif
                                </div>
                                @if ($item->endpoint)
                                    <div class="code-block">
                                        <div class="code-header">
                                            <h3 class="code-label">Endpoint</h3>
                                        </div>
                                        <pre class="code-content"><code>{{ $item->endpoint }}</code></pre>
                                    </div>
                                @endif
                                @if ($item->response)
                                    <div class="code-block">
                                        <div class="code-header">
                                            <h3 class="code-label">Response</h3>
                                        </div>
                                        <pre class="code-content"><code>{{ $item->response }}</code></pre>
                                    </div>
                                @endif

                            </article>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <p>No API documentation available for this category.</p>
                        </div>
                    @endif
                </section>
            @endforeach
        </main>
    </div>
@endsection
