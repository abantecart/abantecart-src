<script type="text/javascript">
    (function () {
        const apiKey = <?php js_echo((string)$this->config->get('config_google_api_key')); ?>;
        const isAutocompleteEnabled = <?php echo $this->config->get('config_google_address_autocomplete') ? 'true' : 'false'; ?>;
        const addressPlaceholder = <?php js_echo((string)$this->language->get('text_google_places_address_placeholder')); ?>;
        if (!isAutocompleteEnabled) {
            return;
        }

        const state = window.__googlePlacesState || {
            loading: false,
            loaded: false,
            countryMap: null
        };
        window.__googlePlacesState = state;

        const addressSelector = 'input[name="address_1"]';

        function dispatchInputChange(el) {
            if (!el) {
                return;
            }
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function setFieldValue(field, value, triggerEvents) {
            if (!field) {
                return;
            }
            field.value = value || '';
            if (triggerEvents !== false) {
                dispatchInputChange(field);
            }
        }

        function getAddressComponent(components, type) {
            if (!Array.isArray(components)) {
                return null;
            }
            for (let i = 0; i < components.length; i++) {
                if (Array.isArray(components[i].types) && components[i].types.indexOf(type) !== -1) {
                    return components[i];
                }
            }
            return null;
        }

        function normalizeString(value) {
            return String(value || '').trim().toLowerCase();
        }

        function normalizeCountryLabel(value) {
            return normalizeString(value)
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/&/g, ' and ')
                .replace(/[^a-z0-9]+/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function buildIntlCountryMap() {
            if (state.countryMap) {
                return state.countryMap;
            }

            state.countryMap = {};
            if (typeof Intl === 'undefined' || typeof Intl.DisplayNames === 'undefined') {
                return state.countryMap;
            }

            const locales = [
                (document.documentElement && document.documentElement.lang) || '',
                navigator.language || '',
                'en'
            ].filter(Boolean);

            let displayNames = null;
            for (let i = 0; i < locales.length; i++) {
                try {
                    displayNames = new Intl.DisplayNames([locales[i]], { type: 'region' });
                    if (displayNames) {
                        break;
                    }
                } catch (e) {
                }
            }

            if (!displayNames) {
                return state.countryMap;
            }

            for (let a = 65; a <= 90; a++) {
                for (let b = 65; b <= 90; b++) {
                    const code = String.fromCharCode(a) + String.fromCharCode(b);
                    const name = displayNames.of(code);
                    if (!name || name === code) {
                        continue;
                    }
                    const key = normalizeCountryLabel(name);
                    if (key && !state.countryMap[key]) {
                        state.countryMap[key] = code.toLowerCase();
                    }
                }
            }

            return state.countryMap;
        }

        function resolveCountryIso2ByText(countryText) {
            const key = normalizeCountryLabel(countryText);
            if (!key) {
                return '';
            }
            return buildIntlCountryMap()[key] || '';
        }

        function setSelectByTextOrValue(select, values) {
            if (!select || !Array.isArray(values) || !values.length) {
                return false;
            }

            const normalized = values
                .filter(Boolean)
                .map(function (v) { return normalizeString(v); });

            if (!normalized.length) {
                return false;
            }

            for (let i = 0; i < select.options.length; i++) {
                const option = select.options[i];
                const text = normalizeString(option.text);
                const value = normalizeString(option.value);
                if (normalized.indexOf(text) !== -1 || normalized.indexOf(value) !== -1) {
                    select.value = option.value;
                    dispatchInputChange(select);
                    return true;
                }
            }

            return false;
        }

        function getFieldsForAddress(address1Field) {
            const form = address1Field ? address1Field.closest('form') : null;
            const root = form || document;

            return {
                address1: address1Field,
                address2: root.querySelector('input[name="address_2"]'),
                city: root.querySelector('input[name="city"]'),
                postcode: root.querySelector('input[name="postcode"]'),
                country: root.querySelector('select[name="country_id"]'),
                zone: root.querySelector('select[name="zone_id"]')
            };
        }

        function resolveCountryIso2(countrySelect) {
            if (!countrySelect || !countrySelect.options || countrySelect.selectedIndex < 0) {
                return '';
            }

            const option = countrySelect.options[countrySelect.selectedIndex];
            if (!option) {
                return '';
            }

            const isoFromText = resolveCountryIso2ByText(option.text);
            if (/^[a-z]{2}$/.test(isoFromText)) {
                return isoFromText;
            }

            return '';
        }

        function applyCountryRestriction(autocomplete, fields) {
            if (!autocomplete || !fields || !fields.country) {
                return;
            }

            const iso2 = resolveCountryIso2(fields.country);
            if (iso2) {
                autocomplete.setComponentRestrictions({ country: iso2 });
            }
        }

        function fillAddressFromPlace(place, fields) {
            if (!place || !Array.isArray(place.address_components) || !fields) {
                return;
            }

            const components = place.address_components;
            const streetNumber = (getAddressComponent(components, 'street_number') || {}).long_name || '';
            const route = (getAddressComponent(components, 'route') || {}).long_name || '';
            const subpremise = (getAddressComponent(components, 'subpremise') || {}).long_name || '';
            const locality = ((getAddressComponent(components, 'locality') || {}).long_name)
                || ((getAddressComponent(components, 'postal_town') || {}).long_name)
                || ((getAddressComponent(components, 'sublocality') || {}).long_name)
                || ((getAddressComponent(components, 'sublocality_level_1') || {}).long_name)
                || '';
            const postalCode = (getAddressComponent(components, 'postal_code') || {}).long_name || '';
            const postalCodeSuffix = (getAddressComponent(components, 'postal_code_suffix') || {}).long_name || '';
            const adminArea1 = getAddressComponent(components, 'administrative_area_level_1');
            const country = getAddressComponent(components, 'country');

            let address1 = (streetNumber + ' ' + route).replace(/\s+/g, ' ').trim();
            if (!address1) {
                address1 = place.name || place.formatted_address || '';
            }
            const fullPostcode = postalCode + (postalCodeSuffix ? '-' + postalCodeSuffix : '');

            setFieldValue(fields.address1, address1, false);
            if (subpremise) {
                setFieldValue(fields.address2, subpremise);
            }
            setFieldValue(fields.city, locality);
            setFieldValue(fields.postcode, fullPostcode);

            if (fields.country && country) {
                const countryChanged = setSelectByTextOrValue(fields.country, [country.short_name, country.long_name]);
                if (countryChanged) {
                    setTimeout(function () {
                        const refreshed = getFieldsForAddress(fields.address1);
                        if (refreshed.zone && adminArea1) {
                            setSelectByTextOrValue(refreshed.zone, [adminArea1.short_name, adminArea1.long_name]);
                        }
                    }, 500);
                } else if (fields.zone && adminArea1) {
                    setSelectByTextOrValue(fields.zone, [adminArea1.short_name, adminArea1.long_name]);
                }
            } else if (fields.zone && adminArea1) {
                setSelectByTextOrValue(fields.zone, [adminArea1.short_name, adminArea1.long_name]);
            }
        }

        function bindAddressField(address1Field) {
            if (!address1Field || address1Field.dataset.googlePlacesBound === '1') {
                return;
            }
            if (!window.google || !google.maps || !google.maps.places) {
                return;
            }

            address1Field.placeholder = addressPlaceholder;
            address1Field.setAttribute('autocomplete', 'new-password');
            address1Field.setAttribute('autocorrect', 'off');
            address1Field.setAttribute('autocapitalize', 'off');
            address1Field.setAttribute('spellcheck', 'false');
            const form = address1Field.closest('form');
            if (form) {
                form.setAttribute('autocomplete', 'off');
            }

            const fields = getFieldsForAddress(address1Field);
            const autocomplete = new google.maps.places.Autocomplete(address1Field, {
                types: ['address'],
                fields: ['address_components', 'formatted_address', 'name']
            });

            applyCountryRestriction(autocomplete, fields);

            if (fields.country && address1Field.dataset.googlePlacesRestrictionBound !== '1') {
                fields.country.addEventListener('change', function () {
                    applyCountryRestriction(autocomplete, getFieldsForAddress(address1Field));
                });
                address1Field.dataset.googlePlacesRestrictionBound = '1';
            }

            autocomplete.addListener('place_changed', function () {
                fillAddressFromPlace(autocomplete.getPlace(), getFieldsForAddress(address1Field));
            });

            address1Field.dataset.googlePlacesBound = '1';
        }

        function getAddressFields() {
            const nodes = document.querySelectorAll(addressSelector);
            const result = [];
            for (let i = 0; i < nodes.length; i++) {
                if (result.indexOf(nodes[i]) === -1) {
                    result.push(nodes[i]);
                }
            }
            return result;
        }

        function initGooglePlacesIfReady() {
            const fields = getAddressFields();
            for (let i = 0; i < fields.length; i++) {
                if (!fields[i].getAttribute('placeholder')) {
                    fields[i].setAttribute('placeholder', addressPlaceholder);
                }
                bindAddressField(fields[i]);
            }
        }

        function ensureGooglePlacesScript() {
            if (state.loaded || state.loading || !apiKey) {
                return;
            }

            state.loading = true;
            const script = document.createElement('script');
            script.async = true;
            script.src = 'https://maps.googleapis.com/maps/api/js?key='
                + encodeURIComponent(apiKey)
                + '&loading=async&libraries=places&callback=abantecartInitGooglePlaces';
            script.onerror = function () {
                state.loading = false;
            };
            document.head.appendChild(script);
        }

        function initGooglePlacesAddressAutocomplete() {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                state.loaded = true;
                initGooglePlacesIfReady();
                return;
            }
            if (getAddressFields().length) {
                ensureGooglePlacesScript();
            }
        }

        window.abantecartInitGooglePlaces = function () {
            state.loading = false;
            state.loaded = true;
            initGooglePlacesIfReady();
        };

        document.addEventListener('DOMContentLoaded', initGooglePlacesAddressAutocomplete);
    })();
</script>
