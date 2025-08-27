import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["search", "results"];

    connect() {
        this.resultsTarget = document.getElementById("client-results");
        this.debounceTimeout = null;
    }

    search(event) {
        const query = event.target.value.trim();

        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
            if (query.length === 0) {
                this.resultsTarget.innerHTML = "";
                return;
            }

            fetch(`/clients?search=${encodeURIComponent(query)}`, {
                headers: { Accept: "text/vnd.turbo-stream.html" },
            })
                .then((response) => response.text())
                .then((html) => {
                    this.resultsTarget.innerHTML = html;
                });
        }, 200);
    }
}
