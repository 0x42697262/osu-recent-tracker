import React, { useState, useEffect } from "react";
import { createRoot } from "react-dom/client";

function Search({ setProfile, setHistory }) {
    const [error, setError] = useState("");

    const [userId, setUserId] = useState();
    useEffect(() => {
        if (!userId) return;
        handleSearchUser();
    }, [userId]);

    const handleSearchUser = async () => {
        const resp = await fetch(`/api/v1/history/${userId}`);
        if (!resp.ok) {
            setError(`Server Returned: ${resp.status}`);
            console.error(`Server Returned: ${resp.status}`);
            return;
        }

        const history = await resp.json();

        setProfile({
            userId: history.id,
            username: history.username,
        });

        setHistory(history.history);
    };

    const handleChangeUserId = (e) => {
        const value = e.target.value;
        setUserId(value);
        setError("");
    };

    return (
        <div>
            <label htmlFor="search" className="sr-only">
                Search
            </label>
            <div className="relative">
                <input
                    name="userId"
                    id="search"
                    placeholder="Enter User ID"
                    onChange={handleChangeUserId}
                    className="h-10 w-64 pl-3 pr-10 rounded-md border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                />
                <span className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                    ⌕
                </span>
            </div>
        </div>
    );
}

function Header({ children }) {
    return (
        <header
            className="h-16  flex items-center px-4"
            style={{ backgroundColor: "#ac396d" }}
        >
            <div className="flex-1">
                <div className="text-sm font-mono text-white">osu!pretend</div>
            </div>

            <nav className="flex items-center space-x-3">{children}</nav>
        </header>
    );
}

function ProfileSection({ profile }) {
    const { userId, username } = profile;
    const epoch = React.useMemo(() => Math.floor(Date.now() / 1_000_000), []);
    const avatarUrl = `https://a.ppy.sh/${userId}?${epoch}.png`;

    return (
        <aside
            className="basis-[30%] max-w-[40%] p-6 text-white"
            style={{ backgroundColor: "#382e32" }}
            aria-label="Profile and stats"
        >
            <div className="space-y-4">
                <div className="flex items-center space-x-4">
                    <img
                        className="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold text-gray-500"
                        src={avatarUrl}
                        alt="osu! Avatar"
                    />

                    <div>
                        <div className="text-lg font-semibold">{username}</div>
                    </div>
                </div>
            </div>
        </aside>
    );
}

function HistoricalStats({ history }) {
    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, "0")}`;
    }

    return (
        <section className="flex-1 p-6">
            <div
                className="rounded-md shadow-sm h-full flex flex-col border"
                style={{ backgroundColor: "#382e32" }}
            >
                {/* Header for historical panel */}
                <div className="px-4 py-3 border-b border-gray-100 flex items-center justify-between text-white">
                    <div>
                        <h2 className="text-sm font-semibold">
                            Historical Scores
                        </h2>
                        <p className="text-xs text-gray-400">
                            Most recent 25 scores
                        </p>
                    </div>
                    <div className="text-xs text-gray-500">
                        Total: {history.length}
                    </div>
                </div>

                <div>
                    <ul className="p-4 space-y-2">
                        {history.map((score, index) => (
                            <li
                                key={index}
                                className="p-3 rounded-md text-white bg-cover bg-center"
                                style={{
                                    backgroundImage: `linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url(https://assets.ppy.sh/beatmaps/${score.beatmap.beatmapset_id}/covers/slimcover.jpg)`,
                                }}
                            >
                                <div>
                                    <div className="flex items-center justify-between">
                                        <div className="flex-1">
                                            <div className="mb-2 font-semibold">
                                                <a
                                                    href={`https://osu.ppy.sh/beatmaps/${score.beatmap.beatmap_id}`}
                                                    className="text-inherit no-underline"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    {score.beatmap.title} by{" "}
                                                    {score.beatmap.artist}
                                                </a>
                                            </div>
                                        </div>

                                        <div className="w-[10%] text-right text-big font-bold">
                                            {score.beatmap.difficulty_rating}★
                                        </div>
                                    </div>

                                    <div className="text-sm font-medium">
                                        {score.beatmap.version} mapped by{" "}
                                        {score.beatmap.creator}
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <div className="mb-2 font-bold">
                                        {score.rank}
                                    </div>
                                    <div>
                                        <div className="text-sm font-medium">
                                            {score.total_score.toLocaleString(
                                                "en-US",
                                            )}
                                        </div>
                                        <div className="text-xs text-gray-400">
                                            {score.classic_total_score.toLocaleString(
                                                "en-US",
                                            ) ?? "-"}
                                        </div>
                                        <div className="text-xs text-gray-400">
                                            Submitted on {score.ended_at}
                                        </div>
                                    </div>

                                    <div className="mt-3">
                                        {/* Row 1: Accuracy / Max Combo / pp */}
                                        <div className="flex gap-4">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    Accuracy
                                                </div>
                                                <div className="text-xs">
                                                    {(
                                                        score.accuracy * 100
                                                    ).toFixed(2)}
                                                    %
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    Max Combo
                                                </div>
                                                <div className="text-xs">
                                                    {score.max_combo}x/
                                                    {score.maximum_statistics
                                                        .great +
                                                        (score
                                                            .maximum_statistics
                                                            .large_tick_hit ??
                                                            0) +
                                                        (score
                                                            .maximum_statistics
                                                            .slider_tail_hit ??
                                                            0) +
                                                        (score
                                                            .maximum_statistics
                                                            .legacy_combo_increase ??
                                                            0)}
                                                    x
                                                </div>
                                            </div>

                                            <div className="flex-1  whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    pp
                                                </div>
                                                <div className="text-xs">
                                                    {score.pp ?? "-"}
                                                </div>
                                            </div>
                                        </div>

                                        {/* Row 2: great / ok / meh / Miss */}
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    great
                                                </div>
                                                <div className="text-xs">
                                                    {score.statistics.great ??
                                                        0}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    ok
                                                </div>
                                                <div className="text-xs">
                                                    {score.statistics.ok ?? 0}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    meh
                                                </div>
                                                <div className="text-xs">
                                                    {score.statistics.meh ?? 0}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    Miss
                                                </div>
                                                <div className="text-xs">
                                                    {score.statistics.miss ?? 0}
                                                </div>
                                            </div>
                                        </div>

                                        {/* Row 3: slider tick / slider end */}
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    SLIDER TICK
                                                </div>
                                                <div className="text-xs">
                                                    <span>
                                                        {score.statistics
                                                            .large_tick_hit ??
                                                            "-"}
                                                    </span>
                                                    <span className="text-xs text-gray-400">
                                                        /
                                                        {score
                                                            .maximum_statistics
                                                            .large_tick_hit ??
                                                            "-"}
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    SLIDER END
                                                </div>
                                                <div className="text-xs">
                                                    <span>
                                                        {score.statistics
                                                            .slider_tail_hit ??
                                                            "-"}
                                                    </span>
                                                    <span className="text-xs text-gray-400">
                                                        /
                                                        {score
                                                            .maximum_statistics
                                                            .slider_tail_hit ??
                                                            "-"}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mt-3">
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    CS
                                                </div>
                                                <div className="text-xs ">
                                                    {score.beatmap.cs}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    AR
                                                </div>
                                                <div className="text-xs ">
                                                    {score.beatmap.ar}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    OD
                                                </div>
                                                <div className="text-xs ">
                                                    {score.beatmap.accuracy}
                                                </div>
                                            </div>
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    HP
                                                </div>
                                                <div className="text-xs ">
                                                    {score.beatmap.drain}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    BPM
                                                </div>
                                                <div className="text-xs ">
                                                    {Number.isInteger(
                                                        score.beatmap.bpm,
                                                    )
                                                        ? score.beatmap.bpm
                                                        : parseFloat(
                                                              score.beatmap.bpm,
                                                          ).toFixed(1)}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium uppercase">
                                                    LENGTH
                                                </div>
                                                <div className="text-xs ">
                                                    {formatTime(
                                                        score.beatmap
                                                            .hit_length,
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="text-sm">#{index + 1}</div>
                                </div>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </section>
    );
}

function App() {
    const [profile, setProfile] = useState({});
    const [history, setHistory] = useState([]);

    return (
        <div
            className="min-h-screen  text-gray-900"
            style={{ backgroundColor: "#1c1719" }}
        >
            <Header>
                <Search setProfile={setProfile} setHistory={setHistory} />
            </Header>
            <main className="flex" style={{ minHeight: "100vh" }}>
                <ProfileSection profile={profile} />
                <HistoricalStats history={history} />
            </main>
        </div>
    );
}

const root = createRoot(document.getElementById("app"));
root.render(<App />);
