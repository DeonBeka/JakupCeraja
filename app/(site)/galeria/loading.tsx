import { PageHeroSkeleton, Sk } from "@/components/skeletons";

export default function Loading() {
  return (
    <>
      <PageHeroSkeleton />
      <section className="section" style={{ paddingTop: 50 }}>
        <div className="container gallery-grid">
          {Array.from({ length: 8 }, (_, i) => (
            <Sk key={i} h="auto" r={14} style={{ aspectRatio: "1 / 1" }} />
          ))}
        </div>
      </section>
    </>
  );
}
