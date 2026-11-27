import Link from "next/link";
import { notFound } from "next/navigation";
import { eq } from "drizzle-orm";
import { ArrowLeft } from "lucide-react";
import { getDb } from "@/lib/db";
import { news } from "@/lib/schema";
import { saveNews } from "../../../actions";
import { ActionForm, ImageField, SubmitButton } from "../../../ui";

export default async function EditNews({ params }: { params: Promise<{ id: string }> }) {
  const { id: raw } = await params;
  const isNew = raw === "i-ri";
  const id = isNew ? 0 : Number(raw);
  const db = await getDb();
  const item = isNew ? undefined : (await db.select().from(news).where(eq(news.id, id)))[0];
  if (!isNew && !item) notFound();
  const today = new Date().toISOString().slice(0, 10);

  return (
    <>
      <Link href="/admin/lajmet" className="a-btn light" style={{ marginBottom: 16 }}>
        <ArrowLeft /> Kthehu te lajmet
      </Link>
      <ActionForm action={saveNews.bind(null, id)}>
        <div className="a-form">
          <section className="a-card">
            <div className="a-card-head">
              <h2>{isNew ? "Lajm i ri" : "Ndrysho lajmin"}</h2>
            </div>
            <div className="a-card-body a-fields">
              <label className="a-field full">
                Titulli *
                <input type="text" name="title" required defaultValue={item?.title} />
              </label>
              <label className="a-field">
                Kategoria
                <input type="text" name="category" placeholder="p.sh. Kulturë, Panair, Promovim" defaultValue={item?.category ?? ""} />
              </label>
              <label className="a-field">
                Data
                <input type="date" name="publishedAt" defaultValue={item?.publishedAt ?? today} />
              </label>
              <label className="a-field full">
                Përmbledhje e shkurtër
                <small>Shfaqet në listën e lajmeve (1–2 fjali).</small>
                <input type="text" name="excerpt" defaultValue={item?.excerpt ?? ""} />
              </label>
              <label className="a-field full">
                Teksti i plotë
                <small>Lini një rresht bosh për paragraf të ri.</small>
                <textarea name="content" rows={12} defaultValue={item?.content ?? ""} />
              </label>
            </div>
            <div className="a-actions">
              <Link href="/admin/lajmet" className="a-btn light">
                Anulo
              </Link>
              <SubmitButton>{isNew ? "Publiko lajmin" : "Ruaj ndryshimet"}</SubmitButton>
            </div>
          </section>
          <section className="a-card a-card-body">
            <ImageField name="image" current={item?.image ?? null} shape="wide" label="Foto kryesore" />
          </section>
        </div>
      </ActionForm>
    </>
  );
}
