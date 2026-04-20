document.addEventListener("DOMContentLoaded", () => {
    const navBtns = document.querySelectorAll('.nav-btn');
    const views = document.querySelectorAll('.app-view');

    navBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetViewId = btn.getAttribute('data-view');
            views.forEach(v => v.classList.add('hidden'));
            document.getElementById(targetViewId).classList.remove('hidden');
            navBtns.forEach(b => {
                b.className = 'nav-btn hover:text-black transition';
            });
            btn.className = 'nav-btn text-black font-semibold border-b-2 border-[#d4f88a] pb-1';
        });
    });
});