import type { Metadata } from "next";
import { Mail, MapPin, Phone } from "lucide-react";
import { getSettings } from "@/lib/queries";
import { ContactForm } from "./contact-form";

export const metadata: Metadata = { title: "Kontakti" };

export default async function ContactPage() {
  const s = await getSettings();
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <span className="tag">Na kontaktoni</span>
          <h1>Jemi këtu për ju</h1>
          <p>Për pyetje rreth botimeve, autorëve, porosive apo bashkëpunimeve, na shkruani.</p>
        </div>
      </section>
      <section className="section">
        <div className="container contact-grid">
          <div className="contact-info">
            <h2 style={{ fontSize: "1.8rem", marginBottom: 6 }}>Flasim për libra, ide dhe bashkëpunime.</h2>
            <p style={{ color: "var(--muted)", marginBottom: 10 }}>
              Shtëpia Botuese &quot;Jakup Ceraja&quot; është e hapur për bashkëpunime me autorë, institucione dhe lexues.
            </p>
            {s.phone && (
              <div className="contact-item">
                <Phone />
                <div>
                  <small>Telefon</small>
                  <a href={`tel:${s.phone.replace(/[^\d+]/g, "")}`}>{s.phone}</a>
                </div>
              </div>
            )}
            {s.email && (
              <div className="contact-item">
                <Mail />
                <div>
                  <small>Email</small>
                  <a href={`mailto:${s.email}`}>{s.email}</a>
                </div>
              </div>
            )}
            {s.address && (
              <div className="contact-item">
                <MapPin />
                <div>
                  <small>Adresa</small>
                  <span>{s.address}</span>
                </div>
              </div>
            )}
          </div>
          <div className="card">
            <h2>Na shkruani</h2>
            <ContactForm />
          </div>
        </div>
      </section>
    </>
  );
}
