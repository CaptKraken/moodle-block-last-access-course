<form action="/" method="get">
    <button id='btnbruh'>bruh</button>
</form>

<script>
    const btnBruh = document.querySelector('#btnbruh');
    btnBruh.addEventListener('click', (e) => {
        const parentEl = btnBruh.closest('form');
        parentEl.addEventListener('submit', (e) => {
            // e.preventDefault();
        })
        console.log(e);
    })
</script>