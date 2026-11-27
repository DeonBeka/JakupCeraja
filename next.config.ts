import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  serverExternalPackages: ["@electric-sql/pglite"],
  agentRules: false,
  images: {
    remotePatterns: [{ protocol: "https", hostname: "*.public.blob.vercel-storage.com" }],
  },
  experimental: {
    serverActions: { bodySizeLimit: "6mb" },
  },
  // Old PHP addresses keep working (links shared on Facebook etc.)
  async redirects() {
    return [
      { source: "/index.php", destination: "/", permanent: true },
      { source: "/about.php", destination: "/rreth-nesh", permanent: true },
      { source: "/books.php", destination: "/librat", permanent: true },
      { source: "/book-details.php", has: [{ type: "query", key: "id", value: "(?<id>\\d+)" }], destination: "/librat/:id", permanent: true },
      { source: "/authors.php", destination: "/autoret", permanent: true },
      { source: "/author-details.php", has: [{ type: "query", key: "id", value: "(?<id>\\d+)" }], destination: "/autoret/:id", permanent: true },
      { source: "/news.php", destination: "/lajmet", permanent: true },
      { source: "/news-details.php", has: [{ type: "query", key: "id", value: "(?<id>\\d+)" }], destination: "/lajmet/:id", permanent: true },
      { source: "/gallery.php", destination: "/galeria", permanent: true },
      { source: "/contact.php", destination: "/kontakti", permanent: true },
      { source: "/search.php", destination: "/kerko", permanent: true },
      { source: "/cart.php", destination: "/shporta", permanent: true },
      { source: "/admin/:path*.php", destination: "/admin", permanent: false },
    ];
  },
};

export default nextConfig;
