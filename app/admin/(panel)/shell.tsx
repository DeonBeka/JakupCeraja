"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import {
  BookOpen,
  ExternalLink,
  Images,
  Layers,
  LayoutDashboard,
  LogOut,
  Mail,
  Menu,
  Newspaper,
  PenLine,
  Settings,
  ShoppingCart,
} from "lucide-react";
import { logout } from "../actions";

const TITLES: Record<string, string> = {
  "/admin": "Paneli",
  "/admin/porosite": "Porositë",
  "/admin/librat": "Librat",
  "/admin/autoret": "Autorët",
  "/admin/kategorite": "Kategoritë",
  "/admin/lajmet": "Lajmet",
  "/admin/galeria": "Galeria",
  "/admin/mesazhet": "Mesazhet",
  "/admin/cilesimet": "Cilësimet",
};

export function AdminShell({
  name,
  newOrders,
  unread,
  children,
}: {
  name: string;
  newOrders: number;
  unread: number;
  children: React.ReactNode;
}) {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);
  useEffect(() => setOpen(false), [pathname]);

  const section = "/" + pathname.split("/").slice(1, 3).join("/");
  const title = TITLES[section] ?? "Admin";
  const nav = [
    { href: "/admin", label: "Paneli", icon: LayoutDashboard },
    { href: "/admin/porosite", label: "Porositë", icon: ShoppingCart, badge: newOrders },
    { href: "/admin/librat", label: "Librat", icon: BookOpen },
    { href: "/admin/autoret", label: "Autorët", icon: PenLine },
    { href: "/admin/kategorite", label: "Kategoritë", icon: Layers },
    { href: "/admin/lajmet", label: "Lajmet", icon: Newspaper },
    { href: "/admin/galeria", label: "Galeria", icon: Images },
    { href: "/admin/mesazhet", label: "Mesazhet", icon: Mail, badge: unread },
    { href: "/admin/cilesimet", label: "Cilësimet", icon: Settings },
  ];

  return (
    <div className={`adm${open ? " open" : ""}`}>
      <aside className="adm-side">
        <div className="adm-brand">
          <img src="/images/logo/logo.png" alt="" width={528} height={372} />
          <div>
            <strong>Jakup Ceraja</strong>
            <span>Admin</span>
          </div>
        </div>
        <nav className="adm-nav">
          {nav.map(({ href, label, icon: Icon, badge }) => (
            <Link key={href} href={href} className={section === href ? "active" : undefined}>
              <Icon /> {label}
              {badge ? <span className="badge">{badge}</span> : null}
            </Link>
          ))}
        </nav>
        <div className="adm-side-foot">
          <a href="/" target="_blank" rel="noreferrer">
            <ExternalLink /> Shiko faqen
          </a>
          <form action={logout}>
            <button type="submit">
              <LogOut /> Dil
            </button>
          </form>
        </div>
      </aside>
      <div className="adm-overlay" onClick={() => setOpen(false)} />
      <div className="adm-main">
        <header className="adm-top">
          <button className="adm-menu" onClick={() => setOpen(true)} aria-label="Menyja">
            <Menu />
          </button>
          <h1>{title}</h1>
          <span className="spacer" />
          <span className="adm-user">Përshëndetje, {name}</span>
        </header>
        <div className="adm-body">{children}</div>
      </div>
    </div>
  );
}
