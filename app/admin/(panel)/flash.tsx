import { CircleCheck } from "lucide-react";

export function Flash({ ok }: { ok?: string }) {
  if (!ok) return null;
  return (
    <div className="a-alert ok">
      <CircleCheck /> {ok === "deleted" ? "U fshi me sukses." : "U ruajt me sukses."}
    </div>
  );
}
