..  include:: /Includes.rst.txt

..  _security:

========
Security
========

A preview shows content that editors wrote, rendered with templates and
scripts of the frontend, inside the backend of the site. Look treats that
content as untrusted and isolates every preview as far as browsers allow.
This chapter explains what is in place and what it means for you.

..  contents::
    :local:
    :depth: 1

..  _security-sandbox:

The preview frame is sandboxed
==============================

Every preview is an :html:`<iframe>` with the :html:`sandbox` attribute set
to :code:`allow-scripts` and nothing else. Browsers then give the frame an
*opaque origin*: the content behaves as if it came from an unknown, foreign
website.

Concretely, content inside a preview

*   **cannot access the backend**: no access to the TYPO3 backend document,
    the editor's session cookie, local storage or session storage;
*   **cannot navigate or open windows**: no links to follow, no popups, no
    redirects of the backend;
*   **cannot submit forms**: a contact form in the preview is just markup;
*   **cannot show dialogs**: no :code:`alert()`, no download prompts;
*   **cannot be clicked**: the frame ignores pointer events, the editor's
    clicks go to the page module controls as usual.

The :html:`referrerpolicy` is :code:`no-referrer`, so requests from the frame
carry no backend URL.

..  _security-csp:

Scripts are limited by a Content Security Policy
================================================

Scripts *may* run inside the frame, otherwise Look could not report the
content height to the page module. Which scripts run is decided by a Content
Security Policy in the preview document:

..  code-block:: text

    script-src 'nonce-<nonce of the backend request>' 'strict-dynamic'

Only script tags that Look itself writes into the document carry that nonce:
its own height script and, with :confval:`allowSiteScripts <flag-allow-site-scripts>`,
the scripts of your frontend build. A :html:`<script>` that arrives inside
the content, for example through an unsafe rich text field, has no nonce and
is refused by the browser. :code:`'strict-dynamic'` lets the trusted scripts
import their own modules.

..  _security-media:

Media are blocked by default
============================

Unless :confval:`allowMedia <flag-allow-media>` is enabled, the same policy
also contains :code:`media-src 'none'; frame-src 'none'`. Video and audio
files are neither downloaded nor played while the page module is open, and
embedded players (YouTube, Vimeo and other iframes) are not loaded either;
videos appear as striped placeholder boxes. Besides bandwidth this avoids a
page module full of playing videos.

The preview document also inherits the Content Security Policy of the TYPO3
backend. Its default only allows assets from the backend's own host, so
external fonts, libraries or images need an explicit extension of that
policy, see :ref:`known-problems-external-hosts`.

..  _security-permissions:

No permissions
==============

Camera, microphone, geolocation, fullscreen, autoplay and the other browser
permissions are not available to the frame. The TYPO3 backend does not grant
them to embedded frames, and the opaque origin of the sandbox denies the rest.

..  _security-implications:

What this means for you
=======================

Enable feature flags deliberately
    The defaults give previews no capabilities beyond CSS. Enabling
    :confval:`allowSiteScripts <flag-allow-site-scripts>` runs your frontend
    build inside the frame. It stays isolated from the backend, but review
    what the build does (tracking, external requests) before enabling it.

Web fonts need a CORS header
    The opaque origin makes web fonts and script modules cross-origin
    requests. Look's own script is a classic script and works without any
    server configuration, but your frontend fonts only load if the server
    answers with :code:`Access-Control-Allow-Origin: *` for their path, see
    :ref:`installation-webserver`. The header is standard practice for public
    static files and exposes nothing the files did not expose before.

Some frontend techniques need adjustments
    External SVG sprites (:html:`<use href="...svg#icon">`) cannot load inside
    an opaque origin, see :ref:`known-problems-icons`. Scripts running in the
    frame (with :confval:`allowSiteScripts <flag-allow-site-scripts>`) have no
    :code:`sessionStorage` or :code:`localStorage` and cannot autoplay media;
    guard such calls in your frontend code as you would for private browsing
    modes.
