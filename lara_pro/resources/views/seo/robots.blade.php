User-agent: *
Allow: /
Disallow: /admin/
Disallow: /p/
Disallow: /vendor/

User-agent: GPTBot
Allow: /
Disallow: /admin/
Disallow: /p/

User-agent: ChatGPT-User
Allow: /
Disallow: /admin/
Disallow: /p/

User-agent: ClaudeBot
Allow: /
Disallow: /admin/
Disallow: /p/

User-agent: PerplexityBot
Allow: /
Disallow: /admin/
Disallow: /p/

Sitemap: {{ rtrim(config('seo.site_url'), '/') }}/sitemap.xml
