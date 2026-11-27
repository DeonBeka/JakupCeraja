import {
  Baby,
  Book,
  BookOpen,
  Brain,
  Feather,
  Globe,
  Landmark,
  Library,
  Newspaper,
  PenLine,
  type LucideIcon,
} from "lucide-react";

export const CATEGORY_ICONS: Record<string, LucideIcon> = {
  book: Book,
  "book-open": BookOpen,
  feather: Feather,
  globe: Globe,
  landmark: Landmark,
  pen: PenLine,
  brain: Brain,
  child: Baby,
  newspaper: Newspaper,
  library: Library,
};

export const CATEGORY_ICON_OPTIONS = [
  { value: "book", label: "Libër" },
  { value: "book-open", label: "Libër i hapur" },
  { value: "feather", label: "Pendë (poezi)" },
  { value: "globe", label: "Glob (përkthime)" },
  { value: "landmark", label: "Monument (histori)" },
  { value: "pen", label: "Stilolaps (ese)" },
  { value: "brain", label: "Tru (psikologji)" },
  { value: "child", label: "Fëmijë" },
  { value: "newspaper", label: "Gazetë" },
  { value: "library", label: "Bibliotekë" },
];

export function CategoryIcon({ name }: { name: string | null | undefined }) {
  const Icon = CATEGORY_ICONS[name ?? ""] ?? Book;
  return <Icon />;
}

export function FacebookIcon() {
  return (
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M13.5 21v-7.5h2.5l.4-3h-2.9V8.6c0-.9.3-1.5 1.5-1.5h1.5V4.4c-.3 0-1.2-.1-2.2-.1-2.2 0-3.7 1.3-3.7 3.8v2.4H8v3h2.6V21h2.9z" />
    </svg>
  );
}

export function InstagramIcon() {
  return (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden="true">
      <rect x="3" y="3" width="18" height="18" rx="5" />
      <circle cx="12" cy="12" r="4" />
      <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
    </svg>
  );
}
