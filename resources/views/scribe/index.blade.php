<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Meriendas Waldorf API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        // Usar siempre el mismo origen que la página para evitar "Failed to fetch" por puerto distinto
        var tryItOutBaseUrl = window.location.origin;
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.8.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.8.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-agenda" class="tocify-header">
                <li class="tocify-item level-1" data-unique="agenda">
                    <a href="#agenda">Agenda</a>
                </li>
                                    <ul id="tocify-subheader-agenda" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="agenda-GETapi-agenda-semana">
                                <a href="#agenda-GETapi-agenda-semana">Agenda de la semana (JSON)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="agenda-GETapi-agenda-semana-csv">
                                <a href="#agenda-GETapi-agenda-semana-csv">Agenda de la semana (CSV)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="agenda-GETapi-agenda-mes">
                                <a href="#agenda-GETapi-agenda-mes">Agenda del mes (JSON)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="agenda-GETapi-agenda-mes-csv">
                                <a href="#agenda-GETapi-agenda-mes-csv">Agenda del mes (CSV)</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-asignaciones" class="tocify-header">
                <li class="tocify-item level-1" data-unique="asignaciones">
                    <a href="#asignaciones">Asignaciones</a>
                </li>
                                    <ul id="tocify-subheader-asignaciones" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="asignaciones-POSTapi-asignaciones--asignacion--intercambiar">
                                <a href="#asignaciones-POSTapi-asignaciones--asignacion--intercambiar">Intercambiar asignación</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-calendario-escolar" class="tocify-header">
                <li class="tocify-item level-1" data-unique="calendario-escolar">
                    <a href="#calendario-escolar">Calendario escolar</a>
                </li>
                                    <ul id="tocify-subheader-calendario-escolar" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="calendario-escolar-GETapi-calendario--anio-">
                                <a href="#calendario-escolar-GETapi-calendario--anio-">Configuración de calendario por año</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-estadisticas" class="tocify-header">
                <li class="tocify-item level-1" data-unique="estadisticas">
                    <a href="#estadisticas">Estadísticas</a>
                </li>
                                    <ul id="tocify-subheader-estadisticas" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="estadisticas-GETapi-estadisticas-resumen">
                                <a href="#estadisticas-GETapi-estadisticas-resumen">Resumen por alumno</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: March 16, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="agenda">Agenda</h1>

    <p>Endpoints para consultar la agenda de meriendas por semana o por mes, en JSON o CSV.</p>

                                <h2 id="agenda-GETapi-agenda-semana">Agenda de la semana (JSON)</h2>

<p>
</p>

<p>Devuelve la agenda de la semana actual o de la semana que comienza en <code>fecha_inicio</code>.</p>

<span id="example-requests-GETapi-agenda-semana">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/agenda/semana" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/agenda/semana"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-agenda-semana">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;fecha&quot;: &quot;2026-03-16&quot;,
            &quot;dia&quot;: &quot;lunes&quot;,
            &quot;cereal&quot;: &quot;Arroz&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 2,
                &quot;nombre&quot;: &quot;Demi&aacute;n&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 12,
                &quot;nombre&quot;: &quot;Renato&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-17&quot;,
            &quot;dia&quot;: &quot;martes&quot;,
            &quot;cereal&quot;: &quot;Cebada&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 8,
                &quot;nombre&quot;: &quot;Lisa&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 4,
                &quot;nombre&quot;: &quot;Felipe&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-18&quot;,
            &quot;dia&quot;: &quot;mi&eacute;rcoles&quot;,
            &quot;cereal&quot;: &quot;Mijo&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 6,
                &quot;nombre&quot;: &quot;Joaquin&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 5,
                &quot;nombre&quot;: &quot;Gael&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-19&quot;,
            &quot;dia&quot;: &quot;jueves&quot;,
            &quot;cereal&quot;: &quot;Centeno&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 1,
                &quot;nombre&quot;: &quot;Bruna&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 9,
                &quot;nombre&quot;: &quot;Lorenzo&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-20&quot;,
            &quot;dia&quot;: &quot;viernes&quot;,
            &quot;cereal&quot;: &quot;Avena&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 10,
                &quot;nombre&quot;: &quot;Olivia&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 3,
                &quot;nombre&quot;: &quot;Emiliano&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-21&quot;,
            &quot;dia&quot;: &quot;s&aacute;bado&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-22&quot;,
            &quot;dia&quot;: &quot;domingo&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-agenda-semana" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-agenda-semana"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-agenda-semana"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-agenda-semana" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-agenda-semana">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-agenda-semana" data-method="GET"
      data-path="api/agenda/semana"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-agenda-semana', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-agenda-semana"
                    onclick="tryItOut('GETapi-agenda-semana');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-agenda-semana"
                    onclick="cancelTryOut('GETapi-agenda-semana');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-agenda-semana"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/agenda/semana</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-agenda-semana"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-agenda-semana"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fecha_inicio</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha_inicio"                data-endpoint="GETapi-agenda-semana"
               value=""
               data-component="query">
    <br>
<p>Fecha de inicio de la semana (YYYY-MM-DD). Opcional; si no se envía, se usa la semana actual. validation.date.</p>
            </div>
                </form>

                    <h2 id="agenda-GETapi-agenda-semana-csv">Agenda de la semana (CSV)</h2>

<p>
</p>

<p>Mismos parámetros que semana (JSON). Descarga un CSV con la agenda de la semana.</p>

<span id="example-requests-GETapi-agenda-semana-csv">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/agenda/semana.csv" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/agenda/semana.csv"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-agenda-semana-csv">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">content-type: text/csv; charset=UTF-8
content-disposition: attachment; filename=agenda_semana.csv
cache-control: no-cache, private
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">﻿Fecha;D&iacute;a;Cereal;Fruta;Elaboraci&oacute;n;&quot;Es feriado&quot;
2026-03-16;lunes;Arroz;Demi&aacute;n;Renato;No
2026-03-17;martes;Cebada;Lisa;Felipe;No
2026-03-18;mi&eacute;rcoles;Mijo;Joaquin;Gael;No
2026-03-19;jueves;Centeno;Bruna;Lorenzo;No
2026-03-20;viernes;Avena;Olivia;Emiliano;No
2026-03-21;s&aacute;bado;;;;S&iacute;
2026-03-22;domingo;;;;S&iacute;
</code>
 </pre>
    </span>
<span id="execution-results-GETapi-agenda-semana-csv" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-agenda-semana-csv"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-agenda-semana-csv"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-agenda-semana-csv" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-agenda-semana-csv">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-agenda-semana-csv" data-method="GET"
      data-path="api/agenda/semana.csv"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-agenda-semana-csv', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-agenda-semana-csv"
                    onclick="tryItOut('GETapi-agenda-semana-csv');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-agenda-semana-csv"
                    onclick="cancelTryOut('GETapi-agenda-semana-csv');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-agenda-semana-csv"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/agenda/semana.csv</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-agenda-semana-csv"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-agenda-semana-csv"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="agenda-GETapi-agenda-mes">Agenda del mes (JSON)</h2>

<p>
</p>

<p>Devuelve la agenda del mes indicado. Requiere <code>anio</code> y <code>mes</code>.</p>

<span id="example-requests-GETapi-agenda-mes">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/agenda/mes?anio=2026&amp;mes=3" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/agenda/mes"
);

const params = {
    "anio": "2026",
    "mes": "3",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-agenda-mes">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;fecha&quot;: &quot;2026-03-03&quot;,
            &quot;dia&quot;: &quot;martes&quot;,
            &quot;cereal&quot;: &quot;Cebada&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 1,
                &quot;nombre&quot;: &quot;Bruna&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 9,
                &quot;nombre&quot;: &quot;Lorenzo&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-04&quot;,
            &quot;dia&quot;: &quot;mi&eacute;rcoles&quot;,
            &quot;cereal&quot;: &quot;Mijo&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 10,
                &quot;nombre&quot;: &quot;Olivia&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 3,
                &quot;nombre&quot;: &quot;Emiliano&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-05&quot;,
            &quot;dia&quot;: &quot;jueves&quot;,
            &quot;cereal&quot;: &quot;Centeno&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 7,
                &quot;nombre&quot;: &quot;Julieta&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 8,
                &quot;nombre&quot;: &quot;Lisa&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-06&quot;,
            &quot;dia&quot;: &quot;viernes&quot;,
            &quot;cereal&quot;: &quot;Avena&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 12,
                &quot;nombre&quot;: &quot;Renato&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 11,
                &quot;nombre&quot;: &quot;Pedro&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-07&quot;,
            &quot;dia&quot;: &quot;s&aacute;bado&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-08&quot;,
            &quot;dia&quot;: &quot;domingo&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-09&quot;,
            &quot;dia&quot;: &quot;lunes&quot;,
            &quot;cereal&quot;: &quot;Arroz&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 5,
                &quot;nombre&quot;: &quot;Gael&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 6,
                &quot;nombre&quot;: &quot;Joaquin&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-10&quot;,
            &quot;dia&quot;: &quot;martes&quot;,
            &quot;cereal&quot;: &quot;Cebada&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 9,
                &quot;nombre&quot;: &quot;Lorenzo&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 2,
                &quot;nombre&quot;: &quot;Demi&aacute;n&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-11&quot;,
            &quot;dia&quot;: &quot;mi&eacute;rcoles&quot;,
            &quot;cereal&quot;: &quot;Mijo&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 3,
                &quot;nombre&quot;: &quot;Emiliano&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 10,
                &quot;nombre&quot;: &quot;Olivia&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-12&quot;,
            &quot;dia&quot;: &quot;jueves&quot;,
            &quot;cereal&quot;: &quot;Centeno&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 11,
                &quot;nombre&quot;: &quot;Pedro&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 7,
                &quot;nombre&quot;: &quot;Julieta&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-13&quot;,
            &quot;dia&quot;: &quot;viernes&quot;,
            &quot;cereal&quot;: &quot;Avena&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 4,
                &quot;nombre&quot;: &quot;Felipe&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 1,
                &quot;nombre&quot;: &quot;Bruna&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-14&quot;,
            &quot;dia&quot;: &quot;s&aacute;bado&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-15&quot;,
            &quot;dia&quot;: &quot;domingo&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-16&quot;,
            &quot;dia&quot;: &quot;lunes&quot;,
            &quot;cereal&quot;: &quot;Arroz&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 2,
                &quot;nombre&quot;: &quot;Demi&aacute;n&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 12,
                &quot;nombre&quot;: &quot;Renato&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-17&quot;,
            &quot;dia&quot;: &quot;martes&quot;,
            &quot;cereal&quot;: &quot;Cebada&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 8,
                &quot;nombre&quot;: &quot;Lisa&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 4,
                &quot;nombre&quot;: &quot;Felipe&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-18&quot;,
            &quot;dia&quot;: &quot;mi&eacute;rcoles&quot;,
            &quot;cereal&quot;: &quot;Mijo&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 6,
                &quot;nombre&quot;: &quot;Joaquin&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 5,
                &quot;nombre&quot;: &quot;Gael&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-19&quot;,
            &quot;dia&quot;: &quot;jueves&quot;,
            &quot;cereal&quot;: &quot;Centeno&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 1,
                &quot;nombre&quot;: &quot;Bruna&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 9,
                &quot;nombre&quot;: &quot;Lorenzo&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-20&quot;,
            &quot;dia&quot;: &quot;viernes&quot;,
            &quot;cereal&quot;: &quot;Avena&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 10,
                &quot;nombre&quot;: &quot;Olivia&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 3,
                &quot;nombre&quot;: &quot;Emiliano&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-21&quot;,
            &quot;dia&quot;: &quot;s&aacute;bado&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-22&quot;,
            &quot;dia&quot;: &quot;domingo&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-23&quot;,
            &quot;dia&quot;: &quot;lunes&quot;,
            &quot;cereal&quot;: &quot;Arroz&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 7,
                &quot;nombre&quot;: &quot;Julieta&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 8,
                &quot;nombre&quot;: &quot;Lisa&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-24&quot;,
            &quot;dia&quot;: &quot;martes&quot;,
            &quot;cereal&quot;: &quot;Cebada&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 12,
                &quot;nombre&quot;: &quot;Renato&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 11,
                &quot;nombre&quot;: &quot;Pedro&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-25&quot;,
            &quot;dia&quot;: &quot;mi&eacute;rcoles&quot;,
            &quot;cereal&quot;: &quot;Mijo&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 5,
                &quot;nombre&quot;: &quot;Gael&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 6,
                &quot;nombre&quot;: &quot;Joaquin&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-26&quot;,
            &quot;dia&quot;: &quot;jueves&quot;,
            &quot;cereal&quot;: &quot;Centeno&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 9,
                &quot;nombre&quot;: &quot;Lorenzo&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 2,
                &quot;nombre&quot;: &quot;Demi&aacute;n&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-27&quot;,
            &quot;dia&quot;: &quot;viernes&quot;,
            &quot;cereal&quot;: &quot;Avena&quot;,
            &quot;fruta&quot;: {
                &quot;id&quot;: 1,
                &quot;nombre&quot;: &quot;Bruna&quot;
            },
            &quot;elaboracion&quot;: {
                &quot;id&quot;: 4,
                &quot;nombre&quot;: &quot;Felipe&quot;
            },
            &quot;es_feriado&quot;: false,
            &quot;etiqueta_feriado&quot;: &quot;&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-28&quot;,
            &quot;dia&quot;: &quot;s&aacute;bado&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-29&quot;,
            &quot;dia&quot;: &quot;domingo&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;Fin de semana&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-30&quot;,
            &quot;dia&quot;: &quot;lunes&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;D&iacute;a sin clase&quot;
        },
        {
            &quot;fecha&quot;: &quot;2026-03-31&quot;,
            &quot;dia&quot;: &quot;martes&quot;,
            &quot;cereal&quot;: &quot;&quot;,
            &quot;fruta&quot;: [],
            &quot;elaboracion&quot;: [],
            &quot;es_feriado&quot;: true,
            &quot;etiqueta_feriado&quot;: &quot;D&iacute;a sin clase&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-agenda-mes" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-agenda-mes"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-agenda-mes"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-agenda-mes" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-agenda-mes">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-agenda-mes" data-method="GET"
      data-path="api/agenda/mes"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-agenda-mes', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-agenda-mes"
                    onclick="tryItOut('GETapi-agenda-mes');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-agenda-mes"
                    onclick="cancelTryOut('GETapi-agenda-mes');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-agenda-mes"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/agenda/mes</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-agenda-mes"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-agenda-mes"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>anio</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="anio"                data-endpoint="GETapi-agenda-mes"
               value="2026"
               data-component="query">
    <br>
<p>Año (entre 2020 y 2100). validation.min validation.max. Example: <code>2026</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>mes</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="mes"                data-endpoint="GETapi-agenda-mes"
               value="3"
               data-component="query">
    <br>
<p>Mes (1 a 12). validation.min validation.max. Example: <code>3</code></p>
            </div>
                </form>

                    <h2 id="agenda-GETapi-agenda-mes-csv">Agenda del mes (CSV)</h2>

<p>
</p>

<p>Mismos parámetros que mes (JSON). Descarga un CSV con la agenda del mes.</p>

<span id="example-requests-GETapi-agenda-mes-csv">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/agenda/mes.csv?anio=2026&amp;mes=3" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/agenda/mes.csv"
);

const params = {
    "anio": "2026",
    "mes": "3",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-agenda-mes-csv">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">content-type: text/csv; charset=UTF-8
content-disposition: attachment; filename=agenda_mes_2026_3.csv
cache-control: no-cache, private
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">﻿Fecha;D&iacute;a;Cereal;Fruta;Elaboraci&oacute;n;&quot;Es feriado&quot;
2026-03-03;martes;Cebada;Bruna;Lorenzo;No
2026-03-04;mi&eacute;rcoles;Mijo;Olivia;Emiliano;No
2026-03-05;jueves;Centeno;Julieta;Lisa;No
2026-03-06;viernes;Avena;Renato;Pedro;No
2026-03-07;s&aacute;bado;;;;S&iacute;
2026-03-08;domingo;;;;S&iacute;
2026-03-09;lunes;Arroz;Gael;Joaquin;No
2026-03-10;martes;Cebada;Lorenzo;Demi&aacute;n;No
2026-03-11;mi&eacute;rcoles;Mijo;Emiliano;Olivia;No
2026-03-12;jueves;Centeno;Pedro;Julieta;No
2026-03-13;viernes;Avena;Felipe;Bruna;No
2026-03-14;s&aacute;bado;;;;S&iacute;
2026-03-15;domingo;;;;S&iacute;
2026-03-16;lunes;Arroz;Demi&aacute;n;Renato;No
2026-03-17;martes;Cebada;Lisa;Felipe;No
2026-03-18;mi&eacute;rcoles;Mijo;Joaquin;Gael;No
2026-03-19;jueves;Centeno;Bruna;Lorenzo;No
2026-03-20;viernes;Avena;Olivia;Emiliano;No
2026-03-21;s&aacute;bado;;;;S&iacute;
2026-03-22;domingo;;;;S&iacute;
2026-03-23;lunes;Arroz;Julieta;Lisa;No
2026-03-24;martes;Cebada;Renato;Pedro;No
2026-03-25;mi&eacute;rcoles;Mijo;Gael;Joaquin;No
2026-03-26;jueves;Centeno;Lorenzo;Demi&aacute;n;No
2026-03-27;viernes;Avena;Bruna;Felipe;No
2026-03-28;s&aacute;bado;;;;S&iacute;
2026-03-29;domingo;;;;S&iacute;
2026-03-30;lunes;;;;S&iacute;
2026-03-31;martes;;;;S&iacute;
</code>
 </pre>
    </span>
<span id="execution-results-GETapi-agenda-mes-csv" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-agenda-mes-csv"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-agenda-mes-csv"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-agenda-mes-csv" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-agenda-mes-csv">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-agenda-mes-csv" data-method="GET"
      data-path="api/agenda/mes.csv"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-agenda-mes-csv', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-agenda-mes-csv"
                    onclick="tryItOut('GETapi-agenda-mes-csv');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-agenda-mes-csv"
                    onclick="cancelTryOut('GETapi-agenda-mes-csv');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-agenda-mes-csv"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/agenda/mes.csv</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-agenda-mes-csv"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-agenda-mes-csv"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>anio</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="anio"                data-endpoint="GETapi-agenda-mes-csv"
               value="2026"
               data-component="query">
    <br>
<p>Año (entre 2020 y 2100). validation.min validation.max. Example: <code>2026</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>mes</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="mes"                data-endpoint="GETapi-agenda-mes-csv"
               value="3"
               data-component="query">
    <br>
<p>Mes (1 a 12). validation.min validation.max. Example: <code>3</code></p>
            </div>
                </form>

                <h1 id="asignaciones">Asignaciones</h1>

    <p>Intercambio de la persona asignada (fruta o elaboración) en una fecha.</p>

                                <h2 id="asignaciones-POSTapi-asignaciones--asignacion--intercambiar">Intercambiar asignación</h2>

<p>
</p>

<p>Cambia el alumno asignado para un rol (fruta o elaboración) en la asignación indicada.
Body: <code>rol</code> (fruta|elaboracion), <code>alumno_nuevo_id</code>, <code>motivo</code> (opcional).</p>

<span id="example-requests-POSTapi-asignaciones--asignacion--intercambiar">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/asignaciones/consequatur/intercambiar" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"rol\": \"fruta\",
    \"alumno_nuevo_id\": 1
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/asignaciones/consequatur/intercambiar"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "rol": "fruta",
    "alumno_nuevo_id": 1
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-asignaciones--asignacion--intercambiar">
</span>
<span id="execution-results-POSTapi-asignaciones--asignacion--intercambiar" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-asignaciones--asignacion--intercambiar"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-asignaciones--asignacion--intercambiar"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-asignaciones--asignacion--intercambiar" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-asignaciones--asignacion--intercambiar">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-asignaciones--asignacion--intercambiar" data-method="POST"
      data-path="api/asignaciones/{asignacion}/intercambiar"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-asignaciones--asignacion--intercambiar', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-asignaciones--asignacion--intercambiar"
                    onclick="tryItOut('POSTapi-asignaciones--asignacion--intercambiar');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-asignaciones--asignacion--intercambiar"
                    onclick="cancelTryOut('POSTapi-asignaciones--asignacion--intercambiar');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-asignaciones--asignacion--intercambiar"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/asignaciones/{asignacion}/intercambiar</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-asignaciones--asignacion--intercambiar"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-asignaciones--asignacion--intercambiar"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>asignacion</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="asignacion"                data-endpoint="POSTapi-asignaciones--asignacion--intercambiar"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>rol</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="rol"                data-endpoint="POSTapi-asignaciones--asignacion--intercambiar"
               value="fruta"
               data-component="body">
    <br>
<p>Rol a intercambiar: <code>fruta</code> o <code>elaboracion</code>. Example: <code>fruta</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>fruta</code></li> <li><code>elaboracion</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>alumno_nuevo_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="alumno_nuevo_id"                data-endpoint="POSTapi-asignaciones--asignacion--intercambiar"
               value="1"
               data-component="body">
    <br>
<p>ID del alumno que tomará el rol. The <code>id</code> of an existing record in the alumnos table. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>motivo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="motivo"                data-endpoint="POSTapi-asignaciones--asignacion--intercambiar"
               value=""
               data-component="body">
    <br>
<p>Motivo del intercambio (opcional, máx. 500 caracteres). validation.max.</p>
        </div>
        </form>

                <h1 id="calendario-escolar">Calendario escolar</h1>

    <p>Endpoints para obtener configuración de calendario (inicio/fin de clases y días sin clase).</p>

                                <h2 id="calendario-escolar-GETapi-calendario--anio-">Configuración de calendario por año</h2>

<p>
</p>

<p>Devuelve <code>fecha_inicio_clases</code>, <code>fecha_fin_clases</code> y los <code>dias_sin_clase</code> del año indicado.</p>

<span id="example-requests-GETapi-calendario--anio-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/calendario/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/calendario/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-calendario--anio-">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-calendario--anio-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-calendario--anio-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-calendario--anio-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-calendario--anio-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-calendario--anio-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-calendario--anio-" data-method="GET"
      data-path="api/calendario/{anio}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-calendario--anio-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-calendario--anio-"
                    onclick="tryItOut('GETapi-calendario--anio-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-calendario--anio-"
                    onclick="cancelTryOut('GETapi-calendario--anio-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-calendario--anio-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/calendario/{anio}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-calendario--anio-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-calendario--anio-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>anio</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="anio"                data-endpoint="GETapi-calendario--anio-"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                    </form>

                <h1 id="estadisticas">Estadísticas</h1>

    <p>Resumen de veces que cada alumno llevó fruta o elaboración, opcionalmente filtrado por año y/o mes.</p>

                                <h2 id="estadisticas-GETapi-estadisticas-resumen">Resumen por alumno</h2>

<p>
</p>

<p>Devuelve estadísticas (veces fruta, veces elaboración) por alumno. Opcional: <code>anio</code>, <code>mes</code>.</p>

<span id="example-requests-GETapi-estadisticas-resumen">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/estadisticas/resumen" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/estadisticas/resumen"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-estadisticas-resumen">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;alumno_id&quot;: 1,
            &quot;nombre&quot;: &quot;Bruna&quot;,
            &quot;fruta&quot;: 5,
            &quot;elaboracion&quot;: 3,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 3,
                    &quot;elaboracion&quot;: 1
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 2
                }
            }
        },
        {
            &quot;alumno_id&quot;: 2,
            &quot;nombre&quot;: &quot;Demi&aacute;n&quot;,
            &quot;fruta&quot;: 3,
            &quot;elaboracion&quot;: 3,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 1
                }
            }
        },
        {
            &quot;alumno_id&quot;: 3,
            &quot;nombre&quot;: &quot;Emiliano&quot;,
            &quot;fruta&quot;: 2,
            &quot;elaboracion&quot;: 4,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                }
            }
        },
        {
            &quot;alumno_id&quot;: 4,
            &quot;nombre&quot;: &quot;Felipe&quot;,
            &quot;fruta&quot;: 3,
            &quot;elaboracion&quot;: 4,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 2
                }
            }
        },
        {
            &quot;alumno_id&quot;: 5,
            &quot;nombre&quot;: &quot;Gael&quot;,
            &quot;fruta&quot;: 3,
            &quot;elaboracion&quot;: 4,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 2
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                }
            }
        },
        {
            &quot;alumno_id&quot;: 6,
            &quot;nombre&quot;: &quot;Joaquin&quot;,
            &quot;fruta&quot;: 4,
            &quot;elaboracion&quot;: 3,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 2
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 1
                }
            }
        },
        {
            &quot;alumno_id&quot;: 7,
            &quot;nombre&quot;: &quot;Julieta&quot;,
            &quot;fruta&quot;: 3,
            &quot;elaboracion&quot;: 3,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 1
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                }
            }
        },
        {
            &quot;alumno_id&quot;: 8,
            &quot;nombre&quot;: &quot;Lisa&quot;,
            &quot;fruta&quot;: 3,
            &quot;elaboracion&quot;: 3,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 1
                }
            }
        },
        {
            &quot;alumno_id&quot;: 9,
            &quot;nombre&quot;: &quot;Lorenzo&quot;,
            &quot;fruta&quot;: 3,
            &quot;elaboracion&quot;: 4,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 2
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                }
            }
        },
        {
            &quot;alumno_id&quot;: 10,
            &quot;nombre&quot;: &quot;Olivia&quot;,
            &quot;fruta&quot;: 4,
            &quot;elaboracion&quot;: 2,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 1
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 1
                }
            }
        },
        {
            &quot;alumno_id&quot;: 11,
            &quot;nombre&quot;: &quot;Pedro&quot;,
            &quot;fruta&quot;: 3,
            &quot;elaboracion&quot;: 3,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 1
                }
            }
        },
        {
            &quot;alumno_id&quot;: 12,
            &quot;nombre&quot;: &quot;Renato&quot;,
            &quot;fruta&quot;: 3,
            &quot;elaboracion&quot;: 3,
            &quot;por_mes&quot;: {
                &quot;2026-3&quot;: {
                    &quot;fruta&quot;: 2,
                    &quot;elaboracion&quot;: 1
                },
                &quot;2026-4&quot;: {
                    &quot;fruta&quot;: 1,
                    &quot;elaboracion&quot;: 2
                }
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-estadisticas-resumen" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-estadisticas-resumen"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-estadisticas-resumen"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-estadisticas-resumen" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-estadisticas-resumen">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-estadisticas-resumen" data-method="GET"
      data-path="api/estadisticas/resumen"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-estadisticas-resumen', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-estadisticas-resumen"
                    onclick="tryItOut('GETapi-estadisticas-resumen');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-estadisticas-resumen"
                    onclick="cancelTryOut('GETapi-estadisticas-resumen');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-estadisticas-resumen"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/estadisticas/resumen</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-estadisticas-resumen"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-estadisticas-resumen"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
