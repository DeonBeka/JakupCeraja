import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { getNewsItem } from "@/lib/queries";
import { dateAl, excerpt, paragraphs } from "@/lib/format";

type Props = { params: Promise<{ id: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const n = await getNewsItem(Number((await params).id));
  return n ? { title: n.title, description: excerpt(n.excerpt || n.content, 160) } : { title: "Lajmi nuk u gjet" };
}

export default async function NewsItemPage({ params }: Props) {
  const id = Number((await params).id);
  const n = Number.isFinite(id) ? await getNewsItem(id) : undefined;
  if (!n) notFound();
  return (
    <section className="section">
      <div className="container" style={{ maxWidth: 820 }}>
        <Link href="/lajmet" className="link-arrow">
          <ArrowLeft /> Të gjitha lajmet
        </Link>
        <div className="news-meta" style={{ marginTop: 30 }}>
          {n.category && <span className="pill">{n.category}</span>}
          <span>{dateAl(n.publishedAt ?? n.createdAt)}</span>
        </div>
        <h1 style={{ fontSize: "clamp(2rem, 4.6vw, 3rem)", margin: "16px 0 26px" }}>{n.title}</h1>
        {n.image && (
          <img src={n.image} alt={n.title} style={{ width: "100%", borderRadius: 14, marginBottom: 30, boxShadow: "var(--shadow-lg)" }} />
        )}
        <div className="prose">
          {paragraphs(n.content || n.excerpt).map((p, i) => (
            <p key={i}>{p}</p>
          ))}
        </div>
      </div>
    </section>
  );
}
