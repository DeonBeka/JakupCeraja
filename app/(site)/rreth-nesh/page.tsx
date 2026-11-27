import type { Metadata } from "next";
import Link from "next/link";
import { ArrowRight, BookOpen, Earth, Feather, Landmark } from "lucide-react";
import { getCategoriesWithCount } from "@/lib/queries";
import { CategoryIcon } from "@/components/icons";

export const metadata: Metadata = { title: "Rreth Nesh" };

export default async function AboutPage() {
  const cats = await getCategoriesWithCount();
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <span className="tag">Rreth nesh</span>
          <h1>Shtëpia Botuese &quot;Jakup Ceraja&quot;</h1>
          <p>Një histori e përkushtimit ndaj librit, kulturës dhe autorëve shqiptarë.</p>
        </div>
      </section>

      <section className="section section-alt">
        <div className="container split">
          <div className="split-img">
            <img src="/images/backgrounds/about-books.jpg" alt="Raft me libra" loading="lazy" />
          </div>
          <div>
            <span className="tag">Kush jemi</span>
            <h2>Libri · Kultura · Dija</h2>
            <p>
              Shtëpia Botuese &quot;Jakup Ceraja&quot; është një shtëpi botuese nga Kosova që i kushtohet botimit të veprave me
              vlerë letrare, kulturore, historike dhe edukative.
            </p>
            <p>
              Misioni ynë është të promovojmë autorët shqiptarë dhe t&apos;u sjellim lexuesve përkthime cilësore nga letërsia
              botërore.
            </p>
            <p>
              Përmes çdo libri synojmë të ruajmë dhe zhvillojmë kulturën e leximit dhe të kontribuojmë në pasurimin e jetës
              kulturore shqiptare.
            </p>
          </div>
        </div>
      </section>

      <section className="section band">
        <div className="container">
          <div className="section-head">
            <span className="tag">Misioni ynë</span>
            <h2>Çfarë na frymëzon?</h2>
            <p>Libri është më shumë se një objekt. Është kujtesë, dije dhe një mënyrë për të lidhur brezat.</p>
          </div>
          <div className="why-grid" style={{ gridTemplateColumns: "repeat(auto-fit, minmax(230px, 1fr))" }}>
            {[
              { icon: BookOpen, t: "Dashuria për librin", d: "Besojmë në fuqinë e librit për të edukuar, frymëzuar dhe afruar njerëzit." },
              { icon: Feather, t: "Autorët shqiptarë", d: "Mbështesim krijimtarinë shqiptare dhe promovojmë zërat e rinj dhe autorët me përvojë." },
              { icon: Earth, t: "Letërsia botërore", d: "Sjellim përkthime cilësore nga letërsia ndërkombëtare për lexuesit shqiptarë." },
              { icon: Landmark, t: "Kultura dhe historia", d: "Ruajmë dhe promovojmë vepra që pasurojnë kujtesën dhe kulturën tonë." },
            ].map(({ icon: Icon, t, d }) => (
              <div className="why-card" key={t}>
                <span className="why-icon">
                  <Icon />
                </span>
                <h3>{t}</h3>
                <p>{d}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {cats.length > 0 && (
        <section className="section">
          <div className="container">
            <div className="section-head">
              <span className="tag">Botimet tona</span>
              <h2>Çfarë botojmë?</h2>
              <p>Një katalog i pasur me vepra për lexues të moshave dhe interesave të ndryshme.</p>
            </div>
            <div className="cat-grid">
              {cats.map((c) => (
                <Link key={c.id} href={`/librat?kategoria=${c.id}`} className="cat-card">
                  <span className="cat-icon">
                    <CategoryIcon name={c.icon} />
                  </span>
                  <h3>{c.name}</h3>
                </Link>
              ))}
            </div>
            <div style={{ textAlign: "center", marginTop: 40 }}>
              <Link href="/librat" className="btn btn-primary">
                Shfleto librat <ArrowRight />
              </Link>
            </div>
          </div>
        </section>
      )}
    </>
  );
}
