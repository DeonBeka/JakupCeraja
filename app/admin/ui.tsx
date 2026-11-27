"use client";

import { useActionState, useState } from "react";
import { useFormStatus } from "react-dom";
import { CircleAlert, CircleCheck, ImagePlus, Trash2, Upload } from "lucide-react";
import type { ActionState } from "./actions";

export function ActionForm({
  action,
  children,
  className,
}: {
  action: (state: ActionState, fd: FormData) => Promise<ActionState>;
  children: React.ReactNode;
  className?: string;
}) {
  const [state, formAction] = useActionState<ActionState, FormData>(action, {});
  return (
    <form action={formAction} className={className}>
      {state.error && (
        <div className="a-alert err">
          <CircleAlert /> {state.error}
        </div>
      )}
      {state.ok && (
        <div className="a-alert ok">
          <CircleCheck /> {state.ok}
        </div>
      )}
      {children}
    </form>
  );
}

export function SubmitButton({ children, pendingText = "Duke ruajtur…" }: { children: React.ReactNode; pendingText?: string }) {
  const { pending } = useFormStatus();
  return (
    <button type="submit" className="a-btn" disabled={pending}>
      {pending ? pendingText : children}
    </button>
  );
}

export function ConfirmButton({
  action,
  message = "A jeni i sigurt? Kjo nuk mund të kthehet.",
  label,
  icon = true,
  className = "a-btn danger",
}: {
  action: () => Promise<void>;
  message?: string;
  label?: string;
  icon?: boolean;
  className?: string;
}) {
  return (
    <form
      action={action}
      onSubmit={(e) => {
        if (!confirm(message)) e.preventDefault();
      }}
    >
      <button type="submit" className={className} aria-label={label ?? "Fshi"}>
        {icon && <Trash2 />}
        {label && <span>{label}</span>}
      </button>
    </form>
  );
}

export function ImageField({
  name,
  current,
  shape = "book",
  label = "Foto",
}: {
  name: string;
  current: string | null;
  shape?: "book" | "wide" | "square";
  label?: string;
}) {
  const [preview, setPreview] = useState<string | null>(current);
  const [removed, setRemoved] = useState(false);
  return (
    <div className="a-upload">
      <span className="a-field">{label}</span>
      <div className={`a-preview ${shape === "book" ? "" : shape}`}>
        {preview ? <img src={preview} alt="" /> : <ImagePlus />}
      </div>
      <label className="a-file">
        <Upload /> {preview ? "Ndrysho foton" : "Zgjidh foton"}
        <input
          type="file"
          name={name}
          accept="image/jpeg,image/png,image/webp"
          onChange={(e) => {
            const f = e.target.files?.[0];
            if (f) {
              setPreview(URL.createObjectURL(f));
              setRemoved(false);
            }
          }}
        />
      </label>
      {preview && (
        <button
          type="button"
          className="a-btn light"
          onClick={() => {
            setPreview(null);
            setRemoved(true);
          }}
        >
          <Trash2 /> Hiq foton
        </button>
      )}
      <input type="hidden" name={`${name}_remove`} value={removed ? "1" : ""} />
      <small style={{ color: "var(--mu)", fontSize: "0.8rem" }}>JPG, PNG ose WEBP, deri 5 MB.</small>
    </div>
  );
}
