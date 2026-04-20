document.addEventListener("DOMContentLoaded", () => {
    const apiUrl = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/egp.json';
    const fallbackAPI = `https://latest.currency-api.pages.dev/v1/currencies/egp.json`;

    function fetchData(url) {
        return fetch(url).then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        });
    }

    fetchData(apiUrl)
        .catch(() => fetchData(fallbackAPI))
        .then(data => {
            const rates = data.egp;
            const date = new Date(data.date).toLocaleDateString('en-US', {
                month: 'long', day: 'numeric', year: 'numeric'
            });

            const currencies = [
                {code: 'USD', symbol: '$', rate: rates.usd},
                {code: 'EUR', symbol: '€', rate: rates.eur},
                {code: 'GBP', symbol: '£', rate: rates.gbp},
                {code: 'SAR', symbol: 'ر.س', rate: rates.sar},
                {code: 'AED', symbol: 'د.إ', rate: rates.aed}];

            let html = '';
            currencies.forEach(currency => {
                const inverseRate = (1 / currency.rate).toFixed(2);
                html += `
                    <div class="p-5 border border-gray-200 rounded-xl bg-white hover:border-[#d4f88a] transition shadow-sm flex flex-col justify-center">
                        <div class="text-sm font-semibold text-gray-500 mb-1 flex items-center justify-between">
                            <span>${currency.code} / EGP</span>
                            <span class="text-lg">${currency.symbol}</span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900">${inverseRate} <span class="text-sm font-normal text-gray-500">EGP</span></div>
                    </div>
                `;
            });

            document.getElementById('currency-container').innerHTML = html;
            document.getElementById('last-updated').innerText = `Last updated: ${date}`;
        })
        .catch(error => {
            console.error('Error fetching currency data:', error);
            document.getElementById('currency-container').innerHTML = `
                <div class="col-span-3 text-center text-red-500 text-sm py-4">
                    Unable to load exchange rates at this time.
                </div>
            `;
            document.getElementById('last-updated').innerText = '';
        })
})