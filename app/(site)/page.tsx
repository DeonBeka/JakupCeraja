import Link from "next/link";
import { ArrowRight, Award, BookOpen, Earth, Feather, Handshake, Heart, Newspaper, PenTool, Truck } from "lucide-react";
import { BookCard } from "@/components/book-card";
import { CategoryIcon } from "@/components/icons";
import { getAuthors, getCategoriesWithCount, getFeaturedBooks, getGallery, getLatestBooks, getNews, getSettings } from "@/lib/queries";
import { dateAl, excerpt, money } from "@/lib/format";
import { GalleryGrid } from "@/components/gallery-grid";

export default async function HomePage() {
  const [featured, latest, cats, authors, news, gallery, settings] = await Promise.all([
    getFeaturedBooks(8),
    getLatestBooks(4),
    getCategoriesWithCount(),
    getAuthors(),
    getNews(3),
    getGallery(6),
    getSettings(),
  ]);
  const shownBooks = featured.length ? featured : latest;

  return (
    <>
      <section className="hero">
        <div className="container hero-inner">
          <div>
            <span className="tag">Shtëpia Botuese &quot;Jakup Ceraja&quot;</span>
            <h1>
              Libra që ruajnë kulturën dhe <em>frymëzojnë</em> breza.
            </h1>
            <p>
              Botime cilësore, autorë shqiptarë dhe përkthime nga letërsia botërore – tani mund t&apos;i porositni online dhe
              t&apos;i merrni në shtëpi.
            </p>
            <div className="hero-actions">
              <Link href="/librat" className="btn btn-primary">
                Shfleto librat <ArrowRight />
              </Link>
              <Link href="/rreth-nesh" className="btn btn-outline">
                Rreth nesh
              </Link>
            </div>
            <div className="hero-stats">
              <div>
                <strong>{cats.reduce((s, c) => s + Number(c.count), 0)}</strong>
                <span>Botime</span>
              </div>
              <div>
                <strong>{authors.length}</strong>
                <span>Autorë</span>
              </div>
              <div>
                <strong>{cats.length}</strong>
                <span>Kategori</span>
              </div>
            </div>
          </div>
          <div className="hero-visual">
            <img src="/images/hero/hero-1.jpg" alt="Libra të hapur në raft" width={1200} height={960} />
            <div className="hero-badge">
              <Truck />
              <div>
                <strong>Porosit online</strong>
                <span>Dërgesë falas në Kosovë</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <div className="section-head">
            <span className="tag">Botimet tona</span>
            <h2>Librat e veçuar</h2>
            <p>Zbuloni disa nga botimet tona të përzgjedhura.</p>
          </div>
          <div className="books-grid">
            {shownBooks.map((b) => (
              <BookCard key={b.id} book={b} />
            ))}
          </div>
          <div style={{ textAlign: "center", marginTop: 40 }}>
            <Link href="/librat" className="btn btn-primary">
              Shiko të gjithë librat <ArrowRight />
            </Link>
          </div>
        </div>
      </section>

      <section className="section section-alt">
        <div className="container split">
          <div className="split-img">
            <img src="/images/backgrounds/about-books.jpg" alt="Raft me libra" width={1000} height={850} loading="lazy" />
          </div>
          <div>
            <span className="tag">Rreth nesh</span>
            <h2>Traditë, kulturë dhe pasion për librin.</h2>
            <p>
              Shtëpia Botuese &quot;Jakup Ceraja&quot; është një shtëpi botuese nga Kosova e përkushtuar botimit të veprave me
              vlerë letrare, kulturore, historike dhe edukative.
            </p>
            <p>
              Ne besojmë se libri është ura që lidh brezat. Përmes botimeve tona promovojmë autorët shqiptarë dhe sjellim
              përkthime cilësore nga letërsia botërore.
            </p>
            <div className="mini-cards">
              <div className="mini-card">
                <BookOpen />
                <h4>Botime cilësore</h4>
                <p>Libra të përzgjedhur me standarde të larta botimi.</p>
              </div>
              <div className="mini-card">
                <Earth />
                <h4>Përkthime</h4>
                <p>Vepra të njohura botërore në gjuhën shqipe.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {cats.length > 0 && (
        <section className="section">
          <div className="container">
            <div className="section-head">
              <span className="tag">Zbulo botimet</span>
              <h2>Kategoritë</h2>
              <p>Eksploroni botimet tona sipas kategorive.</p>
            </div>
            <div className="cat-grid">
              {cats.map((c) => (
                <Link key={c.id} href={`/librat?kategoria=${c.id}`} className="cat-card">
                  <span className="cat-icon">
                    <CategoryIcon name={c.icon} />
                  </span>
                  <div>
                    <h3>{c.name}</h3>
                    <span>{c.count === 1 ? "1 libër" : `${c.count} libra`}</span>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      <section className="section band">
        <div className="container">
          <div className="section-head">
            <span className="tag">Pse ne?</span>
            <h2>Pse të zgjidhni Jakup Ceraja?</h2>
            <p>Përkushtimi ynë ndaj librit dhe kulturës është në qendër të çdo botimi.</p>
          </div>
          <div className="why-grid">
            {[
              { icon: BookOpen, t: "Cilësi botimi", d: "Kujdes i veçantë në përzgjedhjen dhe përgatitjen e çdo botimi." },
              { icon: Earth, t: "Përkthime ndërkombëtare", d: "Sjellim në gjuhën shqipe vepra dhe autorë të njohur nga bota." },
              { icon: Feather, t: "Autorë shqiptarë", d: "Mbështesim dhe promovojmë krijimtarinë e autorëve shqiptarë." },
              { icon: Heart, t: "Dashuri për librin", d: "Libri dhe kultura e leximit janë në themel të punës sonë." },
              { icon: Award, t: "Profesionalizëm", d: "Punojmë me përkushtim për të ofruar botime të denja për lexuesit." },
              { icon: Handshake, t: "Bashkëpunim me autorë", d: "Krijojmë marrëdhënie të qëndrueshme dhe profesionale me autorët." },
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

      {authors.length > 0 && (
        <section className="section section-alt">
          <div className="container">
            <div className="section-head-row">
              <div>
                <span className="tag">Autorët tanë</span>
                <h2>Zëra që pasurojnë letërsinë</h2>
              </div>
              <Link href="/autoret" className="link-arrow">
                Të gjithë autorët <ArrowRight />
              </Link>
            </div>
            <div className="authors-grid">
              {authors.slice(0, 4).map((a) => (
                <Link key={a.id} href={`/autoret/${a.id}`} className="author-card">
                  <div className="author-photo">
                    {a.photo ? <img src={a.photo} alt={a.fullname} loading="lazy" /> : <span className="initials">{initials(a.fullname)}</span>}
                  </div>
                  <div className="author-body">
                    <small>Autor</small>
                    <h3>{a.fullname}</h3>
                    <p>{excerpt(a.biography, 110)}</p>
                    <span className="link-arrow">
                      Shiko profilin <ArrowRight />
                    </span>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      {gallery.length > 0 && (
        <section className="section">
          <div className="container">
            <div className="section-head-row">
              <div>
                <span className="tag">Nga aktivitetet tona</span>
                <h2>Galeria</h2>
              </div>
              <Link href="/galeria" className="link-arrow">
                E gjithë galeria <ArrowRight />
              </Link>
            </div>
            <GalleryGrid items={gallery.map((g) => ({ id: g.id, image: g.image, title: g.title }))} />
          </div>
        </section>
      )}

      {news.length > 0 && (
        <section className="section section-alt">
          <div className="container">
            <div className="section-head-row">
              <div>
                <span className="tag">Lajmet e fundit</span>
                <h2>Nga Shtëpia Botuese</h2>
              </div>
              <Link href="/lajmet" className="link-arrow">
                Të gjitha lajmet <ArrowRight />
              </Link>
            </div>
            <div className="news-grid">
              {news.map((n) => (
                <Link key={n.id} href={`/lajmet/${n.id}`} className="news-card">
                  <div className="news-img">{n.image ? <img src={n.image} alt={n.title} loading="lazy" /> : <Newspaper />}</div>
                  <div className="news-body">
                    <div className="news-meta">
                      {n.category && <span className="pill">{n.category}</span>}
                      <span>{dateAl(n.publishedAt ?? n.createdAt)}</span>
                    </div>
                    <h3>{n.title}</h3>
                    <p>{excerpt(n.excerpt || n.content, 130)}</p>
                    <span className="link-arrow">
                      Lexo lajmin <ArrowRight />
                    </span>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      <section className="section">
        <div className="container">
          <div className="cta">
            <div>
              <h2>Gjeni librin tuaj të radhës.</h2>
              <p>Porositni online – dërgesa në Kosovë është falas, për Shqipëri dhe Maqedoni vetëm {money(Number(settings.shippingPrice))}.</p>
            </div>
            <Link href="/librat" className="btn btn-light">
              <PenTool /> Shfleto katalogun
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}

function initials(name: string) {
  return name
    .split(/\s+/)
    .map((p) => p[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();
}
