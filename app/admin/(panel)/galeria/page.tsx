import { Images, Upload } from "lucide-react";
import { getGallery } from "@/lib/queries";
import { addGallery, deleteGallery, renameGallery } from "../../actions";
import { ActionForm, ConfirmButton, SubmitButton } from "../../ui";
import { Flash } from "../flash";

export const metadata = { title: "Galeria" };

export default async function AdminGallery({ searchParams }: { searchParams: Promise<{ ok?: string }> }) {
  const sp = await searchParams;
  const list = await getGallery();
  return (
    <>
      <Flash ok={sp.ok} />
      <section className="a-card" style={{ marginBottom: 20 }}>
        <div className="a-card-head">
          <h2>Shto foto</h2>
        </div>
        <ActionForm action={addGallery}>
          <div className="a-card-body a-fields">
            <label className="a-field">
              Fotot *<small>Mund të zgjidhni disa foto njëherësh.</small>
              <input type="file" name="images" accept="image/jpeg,image/png,image/webp" multiple required />
            </label>
            <label className="a-field">
              Titulli (opsional)
              <input type="text" name="title" placeholder="p.sh. Panairi i Librit 2026" />
            </label>
          </div>
          <div className="a-actions">
            <span />
            <SubmitButton pendingText="Duke ngarkuar…">
              <Upload /> Ngarko
            </SubmitButton>
          </div>
        </ActionForm>
      </section>

      <section className="a-card">
        <div className="a-card-head">
          <h2>Fotot ({list.length})</h2>
        </div>
        {list.length ? (
          <div className="a-card-body" style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(180px, 1fr))", gap: 14 }}>
            {list.map((g) => (
              <div key={g.id} style={{ border: "1px solid var(--bd)", borderRadius: 12, overflow: "hidden", background: "#fff" }}>
                <img src={g.image} alt="" style={{ width: "100%", aspectRatio: "1", objectFit: "cover" }} />
                <div style={{ padding: 10, display: "flex", gap: 6 }}>
                  <form action={renameGallery.bind(null, g.id)} style={{ display: "flex", gap: 6, flex: 1, minWidth: 0 }}>
                    <input
                      type="text"
                      name="title"
                      defaultValue={g.title ?? ""}
                      placeholder="Titulli"
                      aria-label="Titulli"
                      style={{ flex: 1, minWidth: 0, padding: "7px 9px", border: "1px solid var(--bd)", borderRadius: 8, fontSize: "0.85rem" }}
                    />
                    <button type="submit" className="a-icon-btn" aria-label="Ruaj titullin" style={{ fontSize: "0.75rem", width: "auto", padding: "0 8px" }}>
                      Ruaj
                    </button>
                  </form>
                  <ConfirmButton action={deleteGallery.bind(null, g.id)} message="Ta fshij këtë foto?" className="a-icon-btn danger" />
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="a-empty">
            <Images />
            Galeria është bosh.
          </div>
        )}
      </section>
    </>
  );
}
