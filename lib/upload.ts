import "server-only";
import { put, del } from "@vercel/blob";
import { randomBytes } from "node:crypto";
import { mkdir, writeFile, unlink } from "node:fs/promises";
import path from "node:path";

const ALLOWED = ["image/jpeg", "image/png", "image/webp", "image/gif", "image/avif"];
const MAX_BYTES = 5 * 1024 * 1024;

/**
 * Saves an uploaded image. On Vercel it goes to Vercel Blob; locally to public/uploads/local.
 * Returns the public URL, null when no file was chosen, or throws with a message for the admin.
 */
export async function saveImage(file: FormDataEntryValue | null, folder: string): Promise<string | null> {
  if (!file || typeof file === "string" || file.size === 0) return null;
  if (!ALLOWED.includes(file.type)) throw new Error("Lejohen vetëm foto (JPG, PNG, WEBP).");
  if (file.size > MAX_BYTES) throw new Error("Foto duhet të jetë më e vogël se 5 MB.");

  const ext = (file.name.split(".").pop() || "jpg").toLowerCase().replace(/[^a-z0-9]/g, "") || "jpg";
  const name = `${folder}/${Date.now()}-${randomBytes(4).toString("hex")}.${ext}`;

  if (process.env.BLOB_READ_WRITE_TOKEN) {
    const blob = await put(name, file, { access: "public", contentType: file.type });
    return blob.url;
  }
  if (process.env.VERCEL) throw new Error("Ruajtja e fotove nuk është konfiguruar (BLOB_READ_WRITE_TOKEN).");

  const dir = path.join(process.cwd(), "public", "uploads", "local", folder);
  await mkdir(dir, { recursive: true });
  await writeFile(path.join(process.cwd(), "public", "uploads", "local", name), Buffer.from(await file.arrayBuffer()));
  return `/uploads/local/${name}`;
}

/** Removes an image that was uploaded through the admin (never the original site images). */
export async function removeImage(url: string | null | undefined) {
  if (!url) return;
  try {
    if (url.includes(".blob.vercel-storage.com") && process.env.BLOB_READ_WRITE_TOKEN) {
      await del(url);
    } else if (url.startsWith("/uploads/local/")) {
      await unlink(path.join(process.cwd(), "public", url));
    }
  } catch {
    // the image is already gone – nothing to do
  }
}
