import type { Metadata, Viewport } from "next";
import { Inter, Playfair_Display } from "next/font/google";
import "./globals.css";

const sans = Inter({ subsets: ["latin", "latin-ext"], variable: "--font-sans", display: "swap" });
const serif = Playfair_Display({ subsets: ["latin", "latin-ext"], variable: "--font-serif", display: "swap" });

export const metadata: Metadata = {
  title: { default: 'Shtëpia Botuese "Jakup Ceraja"', template: '%s | Shtëpia Botuese "Jakup Ceraja"' },
  description:
    "Shtëpia Botuese Jakup Ceraja – botime cilësore, autorë shqiptarë, përkthime, poezi, romane dhe libra kulturorë. Porositni online.",
  icons: { icon: "/images/logo/logo.png" },
};

export const viewport: Viewport = { themeColor: "#1f4d3a" };

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="sq" className={`${sans.variable} ${serif.variable}`}>
      <body>{children}</body>
    </html>
  );
}
