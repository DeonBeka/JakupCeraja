import type { Metadata } from "next";
import Link from "next/link";
import { ArrowRight, Search } from "lucide-react";
import { BookCard } from "@/components/book-card";
import { searchAll } from "@/lib/queries";
import { dateAl, excerpt } from "@/lib/format";

export const metadata: Metadata = { title: "Kërko" };

export default async function SearchPage({ searchParams }: { searchParams: Promise<{ q?: string }> }) {
  const q = ((await searchParams).q ?? "").trim().slice(0, 100);
  const results = q ? await searchAll(q) : null;
  const total = results ? results.books.length + results.authors.length + results.news.length : 0;

  return (
    <>
      <section className="page-hero">
        <div className="container">
          <span className="tag">Kërko</span>
          <h1>Çfarë po kërkoni?</h1>
          <form action="/kerko" className="filters" style={{ maxWidth: 620, margin: "28px auto 0" }}>
            <label className="search-box">
              <Search />
              <input type="search" name="q" defaultValue={q} placeholder="Titull libri, autor ose lajm…" autoFocus aria-label="Kërko" />
            </label>
            <button className="btn btn-primary" type="submit">
              Kërko
            </button>
          </form>
        </div>
      </section>
      <section className="section">
        <div className="container">
          {results && (
            <p className="result-count">
              {total === 0 ? `Nuk u gjet asgjë për "${q}".` : `${total} rezultate për "${q}"`}
            </p>
          )}
          {results && results.books.length > 0 && (
            <>
              <h2 style={{ fontSize: "1.6rem", margin: "10px 0 20px" }}>Libra</h2>
              <div className="books-grid">
                {results.books.map((b) => (
                  <BookCard key={b.id} book={b} />
                ))}
              </div>
            </>
          )}
          {results && results.authors.length > 0 && (
            <>
              <h2 style={{ fontSize: "1.6rem", margin: "44px 0 20px" }}>Autorë</h2>
              <div className="authors-grid">
                {results.authors.map((a) => (
                  <Link key={a.id} href={`/autoret/${a.id}`} className="author-card">
                    <div className="author-photo">{a.photo && <img src={a.photo} alt={a.fullname} />}</div>
                    <div className="author-body">
                      <h3>{a.fullname}</h3>
                      <p>{excerpt(a.biography, 100)}</p>
                    </div>
                  </Link>
                ))}
              </div>
            </>
          )}
          {results && results.news.length > 0 && (
            <>
              <h2 style={{ fontSize: "1.6rem", margin: "44px 0 20px" }}>Lajme</h2>
              <div className="news-grid">
                {results.news.map((n) => (
                  <Link key={n.id} href={`/lajmet/${n.id}`} className="news-card">
                    <div className="news-body">
                      <div className="news-meta">{dateAl(n.publishedAt ?? n.createdAt)}</div>
                      <h3>{n.title}</h3>
                      <span className="link-arrow">
                        Lexo <ArrowRight />
                      </span>
                    </div>
                  </Link>
                ))}
              </div>
            </>
          )}
        </div>
      </section>
    </>
  );
}
