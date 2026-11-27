"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { Menu, Search, ShoppingBag, X } from "lucide-react";
import { useCart } from "./cart";

const LINKS = [
  { href: "/", label: "Ballina" },
  { href: "/rreth-nesh", label: "Rreth Nesh" },
  { href: "/librat", label: "Librat" },
  { href: "/autoret", label: "Autorët" },
  { href: "/galeria", label: "Galeria" },
  { href: "/lajmet", label: "Lajmet" },
  { href: "/kontakti", label: "Kontakti" },
];

export function Header() {
  const pathname = usePathname();
  const { count, ready } = useCart();
  const [open, setOpen] = useState(false);

  useEffect(() => setOpen(false), [pathname]);
  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
  }, [open]);

  const isActive = (href: string) => (href === "/" ? pathname === "/" : pathname.startsWith(href));

  return (
    <>
      <header className="site-header">
        <div className="container header-inner">
          <Link href="/" className="logo" aria-label="Ballina">
            <img src="/images/logo/logo.png" alt='Shtëpia Botuese "Jakup Ceraja"' width={528} height={372} />
          </Link>

          <nav className="nav" aria-label="Menyja kryesore">
            {LINKS.map((l) => (
              <Link key={l.href} href={l.href} className={isActive(l.href) ? "active" : undefined}>
                {l.label}
              </Link>
            ))}
          </nav>

          <div className="header-actions">
            <Link href="/kerko" className="icon-btn dark" aria-label="Kërko">
              <Search />
            </Link>
            <Link href="/shporta" className={`icon-btn${pathname === "/shporta" ? " is-active" : ""}`} aria-label={`Shporta (${count})`}>
              <ShoppingBag />
              {ready && count > 0 && <span className="cart-badge">{count}</span>}
            </Link>
            <button className="icon-btn menu-btn" onClick={() => setOpen((o) => !o)} aria-label="Menyja" aria-expanded={open}>
              {open ? <X /> : <Menu />}
            </button>
          </div>
        </div>
      </header>

      <nav className={`mobile-nav${open ? " open" : ""}`} aria-label="Menyja">
        {LINKS.map((l) => (
          <Link key={l.href} href={l.href} className={isActive(l.href) ? "active" : undefined}>
            {l.label}
          </Link>
        ))}
        <Link href="/shporta" className={pathname === "/shporta" ? "active" : undefined}>
          Shporta {ready && count > 0 ? `(${count})` : ""}
        </Link>
      </nav>
    </>
  );
}
