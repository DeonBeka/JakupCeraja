import { BookGridSkeleton, PageHeroSkeleton, Sk } from "@/components/skeletons";

export default function Loading() {
  return (
    <>
      <PageHeroSkeleton />
      <section className="section" style={{ paddingTop: 50 }}>
        <div className="container">
          <div className="sk-row" style={{ justifyContent: "flex-start", gap: 12, marginBottom: 30, flexWrap: "wrap" }}>
            <Sk w="min(360px, 100%)" h={48} r={999} />
            <Sk w={170} h={48} r={999} />
            <Sk w={170} h={48} r={999} />
          </div>
          <BookGridSkeleton count={8} />
        </div>
      </section>
    </>
  );
}
