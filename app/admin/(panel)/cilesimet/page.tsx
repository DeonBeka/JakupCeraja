import { getSettings } from "@/lib/queries";
import { getSession } from "@/lib/auth";
import { saveAccount, saveSettings } from "../../actions";
import { ActionForm, SubmitButton } from "../../ui";

export const metadata = { title: "Cilësimet" };

export default async function AdminSettings() {
  const [s, me] = await Promise.all([getSettings(), getSession()]);
  return (
    <div className="grid-2">
      <section className="a-card">
        <div className="a-card-head">
          <h2>Kontakti dhe dërgesa</h2>
        </div>
        <ActionForm action={saveSettings}>
          <div className="a-card-body a-fields">
            <label className="a-field">
              Telefoni
              <input type="text" name="phone" defaultValue={s.phone ?? ""} />
            </label>
            <label className="a-field">
              Email
              <input type="email" name="email" defaultValue={s.email ?? ""} />
            </label>
            <label className="a-field full">
              Adresa
              <input type="text" name="address" defaultValue={s.address ?? ""} />
            </label>
            <label className="a-field">
              Facebook (linku)
              <input type="text" name="facebook" placeholder="https://facebook.com/…" defaultValue={s.facebook ?? ""} />
            </label>
            <label className="a-field">
              Instagram (linku)
              <input type="text" name="instagram" placeholder="https://instagram.com/…" defaultValue={s.instagram ?? ""} />
            </label>
            <label className="a-field">
              Çmimi i dërgesës (€)
              <input type="number" name="shippingPrice" min="0" step="0.01" defaultValue={Number(s.shippingPrice)} />
            </label>
            <label className="a-field">
              Dërgesë falas mbi (€)
              <input type="number" name="freeShippingFrom" min="0" step="0.01" defaultValue={Number(s.freeShippingFrom)} />
            </label>
          </div>
          <div className="a-actions">
            <span />
            <SubmitButton>Ruaj cilësimet</SubmitButton>
          </div>
        </ActionForm>
      </section>

      <section className="a-card">
        <div className="a-card-head">
          <h2>Llogaria ime</h2>
        </div>
        <ActionForm action={saveAccount}>
          <div className="a-card-body a-fields">
            {me?.id === 0 && (
              <p className="full" style={{ fontSize: "0.88rem", color: "var(--mu)" }}>
                Tani po kyçeni me llogarinë fillestare. Vendosni një password të ri këtu dhe ai do të përdoret nga tani e tutje.
              </p>
            )}
            <label className="a-field full">
              Emri i plotë
              <input type="text" name="fullname" required defaultValue={me?.name} />
            </label>
            <label className="a-field full">
              Username
              <input type="text" name="username" required defaultValue={me?.username} autoComplete="username" />
            </label>
            <label className="a-field">
              Password i ri
              <input type="password" name="password" minLength={8} autoComplete="new-password" />
            </label>
            <label className="a-field">
              Përsërit password-in
              <input type="password" name="confirm" minLength={8} autoComplete="new-password" />
            </label>
            <p className="full" style={{ fontSize: "0.8rem", color: "var(--mu)" }}>
              Lëreni password-in bosh nëse nuk doni ta ndryshoni.
            </p>
          </div>
          <div className="a-actions">
            <span />
            <SubmitButton>Ruaj llogarinë</SubmitButton>
          </div>
        </ActionForm>
      </section>
    </div>
  );
}
