
document.addEventListener('DOMContentLoaded', function () {

    // Menu mobile da loja
    const menuBotao = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-site-menu]');

    if (menuBotao && menu) {
        menuBotao.addEventListener('click', function () {
            menu.classList.toggle('menu-aberto');

            const aberto = menu.classList.contains('menu-aberto');
            menuBotao.setAttribute('aria-expanded', aberto);
        });
    }

    // Botões de aumentar e diminuir quantidade
    document.querySelectorAll('[data-quantidade]').forEach(function (grupo) {
        const campo = grupo.querySelector('input');
        const diminuir = grupo.querySelector('[data-diminuir]');
        const aumentar = grupo.querySelector('[data-aumentar]');

        if (!campo) return;

        if (diminuir) {
            diminuir.addEventListener('click', function () {
                const atual = parseInt(campo.value, 10) || 1;
                campo.value = Math.max(1, atual - 1);
                campo.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        if (aumentar) {
            aumentar.addEventListener('click', function () {
                const atual = parseInt(campo.value, 10) || 1;
                const limite = parseInt(campo.max, 10) || 99;

                campo.value = Math.min(limite, atual + 1);
                campo.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }
    });

    // Impede quantidades menores que 1
    document.querySelectorAll('input[type="number"]').forEach(function (campo) {
        campo.addEventListener('change', function () {
            const minimo = parseInt(campo.min, 10) || 1;
            const valor = parseInt(campo.value, 10);

            campo.value = Math.max(minimo, Number.isNaN(valor) ? minimo : valor);
        });
    });

});
