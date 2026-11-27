import { Sk } from "@/components/skeletons";

export default function Loading() {
  return (
    <>
      <div className="kpis">
        {Array.from({ length: 4 }, (_, i) => (
          <div className="kpi" key={i}>
            <Sk w="50%" h={11} />
            <Sk w="35%" h={26} style={{ marginTop: 10 }} />
          </div>
        ))}
      </div>
      <section className="a-card">
        <div style={{ padding: "16px 20px" }}>
          <Sk w={180} h={18} />
        </div>
        {Array.from({ length: 6 }, (_, i) => (
          <div className="row" key={i}>
            <Sk w={44} h={58} r={6} />
            <div style={{ flex: 1, display: "grid", gap: 8 }}>
              <Sk w="55%" h={14} />
              <Sk w="30%" h={11} />
            </div>
            <Sk w={70} h={16} />
          </div>
        ))}
      </section>
    </>
  );
}
