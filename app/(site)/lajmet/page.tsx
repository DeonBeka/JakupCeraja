import type { Metadata } from "next";
import Link from "next/link";
import { ArrowRight, Newspaper } from "lucide-react";
import { getNews } from "@/lib/queries";
import { dateAl, excerpt } from "@/lib/format";

export const metadata: Metadata = { title: "Lajmet" };

export default async function NewsPage() {
  const items = await getNews();
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <span className="tag">Lajmet</span>
          <h1>Nga Shtëpia Botuese</h1>
          <p>Mësoni të rejat, aktivitetet dhe njoftimet nga Shtëpia Botuese &quot;Jakup Ceraja&quot;.</p>
        </div>
      </section>
      <section className="section">
        <div className="container">
          {items.length ? (
            <div className="news-grid">
              {items.map((n) => (
                <Link key={n.id} href={`/lajmet/${n.id}`} className="news-card">
                  <div className="news-img">{n.image ? <img src={n.image} alt={n.title} loading="lazy" /> : <Newspaper />}</div>
                  <div className="news-body">
                    <div className="news-meta">
                      {n.category && <span className="pill">{n.category}</span>}
                      <span>{dateAl(n.publishedAt ?? n.createdAt)}</span>
                    </div>
                    <h3>{n.title}</h3>
                    <p>{excerpt(n.excerpt || n.content, 150)}</p>
                    <span className="link-arrow">
                      Lexo lajmin <ArrowRight />
                    </span>
                  </div>
                </Link>
              ))}
            </div>
          ) : (
            <div className="empty">
              <Newspaper />
              <h2>Ende nuk ka lajme</h2>
              <p>Rikthehuni së shpejti për të rejat tona.</p>
            </div>
          )}
        </div>
      </section>
    </>
  );
}
