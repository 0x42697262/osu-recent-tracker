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
        <header className="h-16 bg-white border-b border-gray-200 flex items-center px-4">
            <div className="flex-1">
                <div className="text-sm font-mono text-gray-600">
                    osu!pretend
                </div>
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
            className="basis-[30%] max-w-[40%] bg-white border-r border-gray-200 p-6"
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

function HistoricalStats({ History }) {
    // generate 100 scores (static for the exam)
    const scores = Array.from({ length: 25 }, (_, i) => ({
        id: 100 - i,
        value: Math.floor(Math.random() * 1000), // just a placeholder number
        date: `2025-10-${String(1 + (i % 30)).padStart(2, "0")}`,
    }));
    return (
        <section className="flex-1 p-6">
            <div className="bg-white rounded-md shadow-sm h-full flex flex-col border border-gray-200">
                {/* Header for historical panel */}
                <div className="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 className="text-sm font-semibold text-gray-800">
                            Historical Scores
                        </h2>
                        <p className="text-xs text-gray-500">
                            Most recent 25 scores
                        </p>
                    </div>
                    <div className="text-xs text-gray-500">Total: 100</div>
                </div>

                <div>
                    <ul className="p-4 space-y-2">
                        {scores.map((s) => (
                            <li
                                key={s.id}
                                className="p-3 bg-gray-50 border border-gray-100 rounded-md"
                            >
                                <div>
                                    <div className="flex items-center justify-between">
                                        <div className="flex-1">
                                            <div className="mb-2 font-semibold">
                                                Rubik's Cube by Nanahoshi
                                                Kangengakudan
                                            </div>
                                        </div>

                                        <div className="w-[10%] text-right text-big font-bold">
                                            11.31★
                                        </div>
                                    </div>

                                    <div className="text-sm font-medium">
                                        43,252,003,274,489,856,000 mapped by
                                        squishyguppy
                                    </div>
                                </div>

                                <div className="flex items-center justify-between">
                                    <div className="mb-2 font-bold">S</div>
                                    <div>
                                        <div className="text-sm font-medium">
                                            819,411
                                        </div>
                                        <div className="text-xs text-gray-500">
                                            PP 111.79 (395.97 @ 99.51% FC)
                                        </div>
                                        <div className="text-xs text-gray-500">
                                            Submitted on 9 March 2023 19:38
                                        </div>
                                    </div>

                                    <div className="mt-3">
                                        {/* Row 1: Accuracy / Max Combo / pp */}
                                        <div className="flex gap-4">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    Accuracy
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    100.00%
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    Max Combo
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    12,482x
                                                </div>
                                            </div>

                                            <div className="flex-1  whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    pp
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    2686.43
                                                </div>
                                            </div>
                                        </div>

                                        {/* Row 2: great / ok / meh / Miss */}
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    great
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    {s.hits?.great ?? 0}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    ok
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    {s.hits?.ok ?? 0}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    meh
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    {s.hits?.meh ?? 0}
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    Miss
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    {s.hits?.miss ?? 0}
                                                </div>
                                            </div>
                                        </div>

                                        {/* Row 3: slider tick / slider end */}
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    slider tick
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    <span>
                                                        {s.slider?.tick ?? 0}
                                                    </span>
                                                    <span className="text-xs text-gray-400">
                                                        /
                                                        {s.slider?.tickMax ?? 0}
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    slider end
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    <span>
                                                        {s.slider?.end ?? 0}
                                                    </span>
                                                    <span className="text-xs text-gray-400">
                                                        /{s.slider?.endMax ?? 0}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mt-3">
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    CS
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    4
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    AR
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    6
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    OD
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    10
                                                </div>
                                            </div>
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    HP
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    4
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex gap-4 mt-2">
                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    BPM
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    240
                                                </div>
                                            </div>

                                            <div className="flex-1 whitespace-nowrap text-center">
                                                <div className="text-sm font-medium">
                                                    LENGTH
                                                </div>
                                                <div className="text-xs text-gray-700">
                                                    44:42
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="text-sm">#{s.id}</div>
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
        <div className="min-h-screen bg-gray-50 text-gray-900">
            <Header>
                <Search setProfile={setProfile} setHistory={setHistory} />
            </Header>
            <main className="flex" style={{ minHeight: "100vh" }}>
                <ProfileSection profile={profile} />
                <HistoricalStats History={History} />
            </main>
        </div>
    );
}

const root = createRoot(document.getElementById("app"));
root.render(<App />);
