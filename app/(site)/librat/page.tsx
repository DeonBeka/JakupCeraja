import type { Metadata } from "next";
import Link from "next/link";
import { BookOpen, Search } from "lucide-react";
import { BookCard } from "@/components/book-card";
import { getBooks, getCategoriesWithCount } from "@/lib/queries";

export const metadata: Metadata = { title: "Librat" };

export default async function BooksPage({ searchParams }: { searchParams: Promise<Record<string, string | undefined>> }) {
  const sp = await searchParams;
  const q = (sp.q ?? "").trim();
  const category = Number(sp.kategoria) || undefined;
  const sort = sp.rendit ?? "";
  const [books, cats] = await Promise.all([getBooks({ q, category, sort }), getCategoriesWithCount()]);

  const href = (params: Record<string, string | number | undefined>) => {
    const u = new URLSearchParams();
    const merged = { q: q || undefined, kategoria: category, rendit: sort || undefined, ...params };
    Object.entries(merged).forEach(([k, v]) => v !== undefined && v !== "" && u.set(k, String(v)));
    const s = u.toString();
    return s ? `/librat?${s}` : "/librat";
  };

  return (
    <>
      <section className="page-hero">
        <div className="container">
          <span className="tag">Katalogu</span>
          <h1>Librat</h1>
          <p>Zbuloni botimet tona, autorët dhe veprat që pasurojnë kulturën dhe letërsinë shqiptare.</p>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <form className="filters" action="/librat">
            {category && <input type="hidden" name="kategoria" value={category} />}
            <label className="search-box">
              <Search />
              <input type="search" name="q" defaultValue={q} placeholder="Kërko titull ose autor…" aria-label="Kërko libra" />
            </label>
            <select name="rendit" defaultValue={sort} aria-label="Rendit">
              <option value="">Më të rejat</option>
              <option value="title">Titulli A–Z</option>
              <option value="price-asc">Çmimi: nga më i ulëti</option>
              <option value="price-desc">Çmimi: nga më i larti</option>
            </select>
            <button className="btn btn-primary" type="submit">
              Kërko
            </button>
          </form>

          <div className="chips">
            <Link href={href({ kategoria: undefined })} className={`chip${!category ? " active" : ""}`}>
              Të gjitha
            </Link>
            {cats.map((c) => (
              <Link key={c.id} href={href({ kategoria: c.id })} className={`chip${category === c.id ? " active" : ""}`}>
                {c.name} <span style={{ opacity: 0.6 }}>{c.count}</span>
              </Link>
            ))}
          </div>

          {books.length > 0 ? (
            <>
              <p className="result-count">{books.length === 1 ? "1 libër" : `${books.length} libra`}</p>
              <div className="books-grid">
                {books.map((b) => (
                  <BookCard key={b.id} book={b} />
                ))}
              </div>
            </>
          ) : (
            <div className="empty">
              <BookOpen />
              <h2>Nuk u gjet asnjë libër</h2>
              <p>Provoni një kërkim tjetër ose shikoni të gjitha kategoritë.</p>
              <Link href="/librat" className="btn btn-primary">
                Shiko të gjithë librat
              </Link>
            </div>
          )}
        </div>
      </section>
    </>
  );
}
