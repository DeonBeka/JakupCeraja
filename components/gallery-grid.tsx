"use client";

import { useEffect, useState } from "react";
import { X } from "lucide-react";

type Item = { id: number; image: string; title: string | null };

export function GalleryGrid({ items }: { items: Item[] }) {
  const [open, setOpen] = useState<Item | null>(null);

  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => e.key === "Escape" && setOpen(null);
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open]);

  return (
    <>
      <div className="gallery-grid">
        {items.map((g) => (
          <button key={g.id} type="button" className="gallery-item" onClick={() => setOpen(g)} aria-label={g.title ?? "Foto"}>
            <img src={g.image} alt={g.title ?? ""} loading="lazy" />
            {g.title && <span>{g.title}</span>}
          </button>
        ))}
      </div>
      {open && (
        <div className="lightbox" role="dialog" aria-modal="true" onClick={() => setOpen(null)}>
          <button type="button" className="icon-btn" onClick={() => setOpen(null)} aria-label="Mbyll">
            <X />
          </button>
          <figure onClick={(e) => e.stopPropagation()} style={{ margin: 0 }}>
            <img src={open.image} alt={open.title ?? ""} />
            {open.title && <p>{open.title}</p>}
          </figure>
        </div>
      )}
    </>
  );
}
