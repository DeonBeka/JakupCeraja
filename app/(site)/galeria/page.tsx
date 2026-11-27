import type { Metadata } from "next";
import { Images } from "lucide-react";
import { GalleryGrid } from "@/components/gallery-grid";
import { getGallery } from "@/lib/queries";

export const metadata: Metadata = { title: "Galeria" };

export default async function GalleryPage() {
  const items = await getGallery();
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <span className="tag">Nga aktivitetet tona</span>
          <h1>Galeria</h1>
          <p>Momente nga aktivitetet, promovimet dhe botimet e Shtëpisë Botuese &quot;Jakup Ceraja&quot;.</p>
        </div>
      </section>
      <section className="section">
        <div className="container">
          {items.length ? (
            <GalleryGrid items={items.map((g) => ({ id: g.id, image: g.image, title: g.title }))} />
          ) : (
            <div className="empty">
              <Images />
              <h2>Galeria është bosh</h2>
              <p>Fotot do të shtohen së shpejti.</p>
            </div>
          )}
        </div>
      </section>
    </>
  );
}
