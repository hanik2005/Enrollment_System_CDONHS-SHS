document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById("provinceSelect");
    const citySelect = document.getElementById("citySelect");
    const barangaySelect = document.getElementById("barangaySelect");
    const provinceInput = document.getElementById("provinceInput");
    const cityInput = document.getElementById("cityInput");
    const barangayInput = document.getElementById("barangayInput");

    // Guard: do nothing on pages without address dropdowns.
    if (!provinceSelect || !citySelect || !barangaySelect) {
        return;
    }

    const API = "https://psgc.gitlab.io/api";

    function createOption(value, label, extra = {}) {
        const option = document.createElement("option");
        option.value = value;
        option.textContent = label;

        Object.entries(extra).forEach(([key, val]) => {
            option.dataset[key] = val;
        });

        return option;
    }

    function resetSelect(select, placeholder, includeOther = true) {
        select.innerHTML = "";
        select.appendChild(createOption("", placeholder));

        if (includeOther) {
            select.appendChild(createOption("Other", "Other (Specify)"));
        }
    }

    function clearLinkedInput(inputElement) {
        if (!inputElement) {
            return;
        }

        inputElement.value = "";
        inputElement.style.display = "none";
        inputElement.required = false;
    }

    function setManualFallback(select, placeholder) {
        resetSelect(select, placeholder, true);
        const infoOption = createOption("", "Unable to load from PSGC API. Choose Other (Specify).");
        infoOption.disabled = true;
        select.appendChild(infoOption);
    }

    async function fetchJson(url) {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status} - ${url}`);
        }

        return response.json();
    }

    async function loadProvinces() {
        try {
            resetSelect(provinceSelect, "Select Province");

            const provinces = await fetchJson(`${API}/provinces/`);

            provinces
                .sort((a, b) => a.name.localeCompare(b.name))
                .forEach((province) => {
                    // Store readable name in value for DB inserts.
                    // Keep code in data-code for the next API call.
                    provinceSelect.appendChild(
                        createOption(province.name, province.name, { code: province.code })
                    );
                });
        } catch (error) {
            console.error("Failed to load provinces:", error);
            setManualFallback(provinceSelect, "Select Province");
        }
    }

    async function loadCitiesAndMunicipalities(provinceCode) {
        resetSelect(citySelect, "Select City / Municipality");
        resetSelect(barangaySelect, "Select Barangay");
        clearLinkedInput(cityInput);
        clearLinkedInput(barangayInput);

        if (!provinceCode) {
            return;
        }

        try {
            const [cities, municipalities] = await Promise.all([
                fetchJson(`${API}/provinces/${provinceCode}/cities/`),
                fetchJson(`${API}/provinces/${provinceCode}/municipalities/`)
            ]);

            const places = [
                ...cities.map((item) => ({ ...item, placeType: "city" })),
                ...municipalities.map((item) => ({ ...item, placeType: "municipality" }))
            ].sort((a, b) => a.name.localeCompare(b.name));

            places.forEach((place) => {
                // Value is name for readable storage; code+type for barangay API lookup.
                citySelect.appendChild(
                    createOption(place.name, place.name, {
                        code: place.code,
                        type: place.placeType
                    })
                );
            });
        } catch (error) {
            console.error("Failed to load cities/municipalities:", error);
            setManualFallback(citySelect, "Select City / Municipality");
            setManualFallback(barangaySelect, "Select Barangay");
        }
    }

    async function loadBarangays(placeCode, placeType) {
        resetSelect(barangaySelect, "Select Barangay");
        clearLinkedInput(barangayInput);

        if (!placeCode) {
            return;
        }

        let endpointOrder = ["cities", "municipalities"];
        if (placeType === "municipality") {
            endpointOrder = ["municipalities", "cities"];
        } else if (placeType === "city") {
            endpointOrder = ["cities", "municipalities"];
        }

        let loaded = false;
        let lastError = null;

        for (const endpoint of endpointOrder) {
            try {
                const barangays = await fetchJson(`${API}/${endpoint}/${placeCode}/barangays/`);
                barangays
                    .sort((a, b) => a.name.localeCompare(b.name))
                    .forEach((barangay) => {
                        barangaySelect.appendChild(createOption(barangay.name, barangay.name));
                    });
                loaded = true;
                break;
            } catch (error) {
                lastError = error;
            }
        }

        if (!loaded) {
            console.error("Failed to load barangays:", lastError);
            setManualFallback(barangaySelect, "Select Barangay");
        }
    }

    provinceSelect.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const provinceCode = selectedOption?.dataset?.code || "";
        if (provinceInput && this.value && this.value !== "Other") {
            provinceInput.value = this.value;
        }
        loadCitiesAndMunicipalities(provinceCode);
    });

    citySelect.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const placeCode = selectedOption?.dataset?.code || "";
        const placeType = selectedOption?.dataset?.type || "";
        if (cityInput && this.value && this.value !== "Other") {
            cityInput.value = this.value;
        }
        loadBarangays(placeCode, placeType);
    });

    barangaySelect.addEventListener("change", function () {
        if (barangayInput && this.value && this.value !== "Other") {
            barangayInput.value = this.value;
        }
    });

    loadProvinces();
});
