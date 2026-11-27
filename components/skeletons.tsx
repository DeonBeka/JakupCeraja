// Grey "shimmer" placeholders shown while a page is loading.

import type { CSSProperties } from "react";

export function Sk({ w = "100%", h = 14, r, style, className = "" }: { w?: number | string; h?: number | string; r?: number; style?: CSSProperties; className?: string }) {
  return <span aria-hidden className={`sk ${className}`} style={{ width: w, height: h, borderRadius: r, ...style }} />;
}

export function PageHeroSkeleton() {
  return (
    <section className="page-hero">
      <div className="container sk-center">
        <Sk w={110} h={12} />
        <Sk w="min(420px, 80%)" h={40} r={10} />
        <Sk w="min(520px, 90%)" h={14} />
      </div>
    </section>
  );
}

export function BookGridSkeleton({ count = 8 }: { count?: number }) {
  return (
    <div className="books-grid">
      {Array.from({ length: count }, (_, i) => (
        <div className="book-card sk-card" key={i}>
          <Sk className="sk-cover" h="auto" r={0} />
          <div className="book-body sk-stack">
            <Sk w="40%" h={10} />
            <Sk w="85%" h={18} />
            <Sk w="55%" h={12} />
            <div className="sk-row" style={{ marginTop: 10 }}>
              <Sk w={70} h={20} />
              <Sk w={44} h={36} r={999} />
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}

export function CardGridSkeleton({ count = 6, media = "4 / 3", grid = "news-grid" }: { count?: number; media?: string; grid?: string }) {
  return (
    <div className={grid}>
      {Array.from({ length: count }, (_, i) => (
        <div className="news-card sk-card" key={i}>
          <Sk h="auto" r={0} style={{ aspectRatio: media }} />
          <div className="sk-stack" style={{ padding: 20 }}>
            <Sk w="35%" h={10} />
            <Sk w="80%" h={18} />
            <Sk w="95%" h={12} />
            <Sk w="70%" h={12} />
          </div>
        </div>
      ))}
    </div>
  );
}

export function DetailSkeleton() {
  return (
    <section className="section" style={{ paddingTop: 50 }}>
      <div className="container detail">
        <Sk className="sk-cover" h="auto" r={10} style={{ maxWidth: 340 }} />
        <div className="sk-stack" style={{ gap: 14 }}>
          <Sk w={120} h={12} />
          <Sk w="80%" h={42} r={10} />
          <Sk w={180} h={16} />
          <Sk w="100%" h={74} r={14} style={{ marginTop: 10 }} />
          <div className="sk-row" style={{ justifyContent: "flex-start", gap: 12 }}>
            <Sk w={190} h={50} r={999} />
            <Sk w={140} h={50} r={999} />
          </div>
          <Sk w="100%" h={12} style={{ marginTop: 18 }} />
          <Sk w="96%" h={12} />
          <Sk w="88%" h={12} />
          <Sk w="60%" h={12} />
        </div>
      </div>
    </section>
  );
}
