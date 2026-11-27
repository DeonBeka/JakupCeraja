import Link from "next/link";
import { ArrowRight, Mail, MapPin, Phone } from "lucide-react";
import { getSettings } from "@/lib/queries";
import { FacebookIcon, InstagramIcon } from "./icons";

export async function Footer() {
  const s = await getSettings();
  return (
    <footer className="site-footer">
      <div className="container footer-grid">
        <div>
          <Link href="/" className="footer-logo" aria-label="Ballina">
            <img src="/images/logo/logo.png" alt='Shtëpia Botuese "Jakup Ceraja"' width={528} height={372} />
          </Link>
          <p>Shtëpia Botuese &quot;Jakup Ceraja&quot; – promovojmë librin, dijen dhe kulturën shqiptare.</p>
          {(s.facebook || s.instagram) && (
            <div className="socials">
              {s.facebook && (
                <a href={s.facebook} target="_blank" rel="noreferrer" aria-label="Facebook">
                  <FacebookIcon />
                </a>
              )}
              {s.instagram && (
                <a href={s.instagram} target="_blank" rel="noreferrer" aria-label="Instagram">
                  <InstagramIcon />
                </a>
              )}
            </div>
          )}
        </div>

        <div>
          <h4>Navigimi</h4>
          <ul>
            <li><Link href="/">Ballina</Link></li>
            <li><Link href="/rreth-nesh">Rreth Nesh</Link></li>
            <li><Link href="/librat">Librat</Link></li>
            <li><Link href="/autoret">Autorët</Link></li>
            <li><Link href="/lajmet">Lajmet</Link></li>
            <li><Link href="/galeria">Galeria</Link></li>
            <li><Link href="/kontakti">Kontakti</Link></li>
          </ul>
        </div>

        <div>
          <h4>Na kontaktoni</h4>
          <ul className="footer-contact">
            {s.phone && (
              <li>
                <Phone /> <a href={`tel:${s.phone.replace(/[^\d+]/g, "")}`}>{s.phone}</a>
              </li>
            )}
            {s.email && (
              <li>
                <Mail /> <a href={`mailto:${s.email}`}>{s.email}</a>
              </li>
            )}
            {s.address && (
              <li>
                <MapPin /> {s.address}
              </li>
            )}
          </ul>
        </div>

        <div>
          <h4>Porosit online</h4>
          <p style={{ marginBottom: 18 }}>
            Shtoni librat në shportë dhe ne jua dërgojmë në shtëpi. Pagesa bëhet në dorëzim.
          </p>
          <Link href="/librat" className="btn btn-light">
            Shiko librat <ArrowRight />
          </Link>
        </div>
      </div>
      <div className="container footer-bottom">
        <span>© {new Date().getFullYear()} Shtëpia Botuese &quot;Jakup Ceraja&quot;. Të gjitha të drejtat e rezervuara.</span>
        <Link href="/admin">Hyrja e administratorit</Link>
      </div>
    </footer>
  );
}
