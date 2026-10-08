{{--
    Alerta global de validación.
    Cuando CUALQUIER componente Livewire falla su validación, muestra un toast
    rojo (arriba a la derecha) con los mensajes de validación a corregir.

    Usa un toast propio en HTML/CSS puro (sin jQuery ni librerías externas),
    para no depender de toastr/jQuery que no siempre están disponibles.

    Es puramente aditivo: no altera la validación existente ni los mensajes
    @error por campo.
--}}
<style>
    #ipostel-validation-toast-wrap {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 360px;
        pointer-events: none;
    }
    .ipostel-vtoast {
        pointer-events: auto;
        background: #d9534f;
        color: #fff;
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        padding: 14px 16px 14px 44px;
        position: relative;
        font-family: "Inter", system-ui, sans-serif;
        font-size: 13px;
        line-height: 1.35;
        opacity: 0;
        transform: translateX(20px);
        transition: opacity .25s ease, transform .25s ease;
    }
    .ipostel-vtoast.show { opacity: .96; transform: translateX(0); }
    .ipostel-vtoast .vtoast-title {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 4px;
        display: block;
    }
    .ipostel-vtoast .vtoast-icon {
        position: absolute;
        left: 14px;
        top: 15px;
        width: 18px;
        height: 18px;
    }
    .ipostel-vtoast .vtoast-close {
        position: absolute;
        top: 8px;
        right: 10px;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        opacity: .8;
        background: none;
        border: 0;
        color: #fff;
    }
    .ipostel-vtoast .vtoast-close:hover { opacity: 1; }
    .ipostel-vtoast ul { margin: 0; padding-left: 16px; }
    .ipostel-vtoast li { margin: 2px 0; }
</style>
<script>
    (function () {
        // DEBUG en false: no imprime nada en consola. Ponlo en true solo para diagnostico.
        const DEBUG = false;

        function ensureWrap() {
            let wrap = document.getElementById('ipostel-validation-toast-wrap');
            if (!wrap) {
                wrap = document.createElement('div');
                wrap.id = 'ipostel-validation-toast-wrap';
                document.body.appendChild(wrap);
            }
            return wrap;
        }

        function escapeHtml(s) {
            return String(s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Evita mostrar el mismo toast dos veces casi simultaneamente
        // (protege ante instancias duplicadas de Livewire que disparan el hook 2 veces).
        let lastToastKey = '';
        let lastToastTime = 0;

        function showValidationToast(messages) {
            if (!messages || messages.length === 0) return;

            const key = messages.join('|');
            const now = Date.now();
            if (key === lastToastKey && (now - lastToastTime) < 1500) {
                return; // mismo contenido en menos de 1.5s: es un duplicado, se ignora
            }
            lastToastKey = key;
            lastToastTime = now;

            const wrap = ensureWrap();

            const toast = document.createElement('div');
            toast.className = 'ipostel-vtoast';

            const items = messages.map(function (m) {
                return '<li>' + escapeHtml(m) + '</li>';
            }).join('');

            toast.innerHTML =
                '<svg class="vtoast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" ' +
                'd="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>' +
                '<button type="button" class="vtoast-close" aria-label="Cerrar">&times;</button>' +
                '<span class="vtoast-title">Corrija los siguientes campos</span>' +
                '<ul>' + items + '</ul>';

            wrap.appendChild(toast);
            // fuerza reflow para la animacion de entrada
            void toast.offsetWidth;
            toast.classList.add('show');

            function remove() {
                toast.classList.remove('show');
                setTimeout(function () { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 250);
            }
            toast.querySelector('.vtoast-close').addEventListener('click', remove);
            setTimeout(remove, 7000);
        }

        function messagesFromErrors(errors) {
            const out = [];
            if (!errors || typeof errors !== 'object') return out;
            Object.keys(errors).forEach(function (key) {
                const val = errors[key];
                if (Array.isArray(val)) {
                    val.forEach(function (m) { if (m && out.indexOf(m) === -1) out.push(m); });
                } else if (typeof val === 'string' && out.indexOf(val) === -1) {
                    out.push(val);
                }
            });
            return out;
        }

        // Busca 'errors' recursivamente en la respuesta (robusto a cambios de formato).
        function deepFindErrors(obj, depth) {
            if (!obj || typeof obj !== 'object' || depth > 6) return null;
            if (obj.errors && typeof obj.errors === 'object' && Object.keys(obj.errors).length > 0) {
                return obj.errors;
            }
            for (const k in obj) {
                if (!Object.prototype.hasOwnProperty.call(obj, k)) continue;
                let v = obj[k];
                if (typeof v === 'string' && v.length && (v[0] === '{' || v[0] === '[')) {
                    try { v = JSON.parse(v); } catch (e) {}
                }
                if (v && typeof v === 'object') {
                    const found = deepFindErrors(v, depth + 1);
                    if (found) return found;
                }
            }
            return null;
        }

        function handleResult(result) {
            try {
                let errors = null;
                const json = result && result.json ? result.json : result;

                if (json && Array.isArray(json.components)) {
                    json.components.forEach(function (comp) {
                        if (errors) return;
                        if (comp && comp.effects && comp.effects.errors) errors = comp.effects.errors;
                    });
                }
                if (!errors) errors = deepFindErrors(result, 0);

                if (DEBUG) console.log('[validation-alert] errors =', errors);

                const messages = messagesFromErrors(errors);
                if (messages.length > 0) showValidationToast(messages);
            } catch (e) {
                if (DEBUG) console.error('[validation-alert] error interno', e);
            }
        }

        function registerHook() {
            if (typeof Livewire === 'undefined' || !Livewire.hook) return false;

            // El proyecto puede tener varias instancias de Livewire; el commit de
            // algunos modulos (p.ej. correspondencia) ocurre en una instancia distinta.
            // Por eso registramos el hook en CADA instancia (una vez por instancia),
            // y la deduplicacion se hace en showValidationToast (ventana de 1.5s),
            // asi el toast nunca se muestra dos veces.
            if (Livewire.__ipostelValidationHooked) return true;
            Livewire.__ipostelValidationHooked = true;

            Livewire.hook('commit', function (params) {
                const succeed = params.succeed;
                if (typeof succeed !== 'function') return;
                succeed(function (result) { handleResult(result); });
            });
            if (DEBUG) console.log('[validation-alert] hook registrado');
            return true;
        }

        document.addEventListener('livewire:init', registerHook);
        if (window.Livewire && window.Livewire.hook) registerHook();
    })();
</script>
