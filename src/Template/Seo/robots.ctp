# Rember, lines starting with # are comments and will be ignored

User-agent: *

Disallow: /cgi-bin/
Disallow: /*.js$
Disallow: /*.css$

# Everything else may be searched/indexed


# Google Image
User-agent: Googlebot-Image
Disallow:
Allow: /*

# Google AdSense
User-agent: Mediapartners-Google*
Disallow:
Allow: /*


# Sitemap (Update with your Domain, if you have a sitemap)
Sitemap: https://www.verkkotesti.fi/sitemap.xml