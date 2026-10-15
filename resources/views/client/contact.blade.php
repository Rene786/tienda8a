<x-layouts::app title="Contacto | TechStore">
    <div class="space-y-10">
        <flux:card class="overflow-hidden bg-gradient-to-br from-[#0F172A] via-[#1D4ED8] to-[#2563EB] px-6 py-14 text-center text-white shadow-xl sm:px-12 sm:py-16">
            <div class="mx-auto max-w-3xl">
                <flux:badge color="blue" class="mb-5">Contacto</flux:badge>
                <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">¿Cómo podemos ayudarte?</h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-zinc-300 sm:text-lg">
                    Queremos que encuentres lo que necesitas. Cuéntanos tu duda y pronto podrás comunicarte directamente desde aquí.
                </p>
            </div>
        </flux:card>

        <section class="grid items-start gap-6 lg:grid-cols-5">
            <flux:card class="lg:col-span-3">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:badge color="blue" class="mb-4">Envíanos un mensaje</flux:badge>
                        <h2 class="text-2xl font-semibold tracking-tight">Estamos para escucharte</h2>
                        <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                            Completa tus datos y describe brevemente en qué podemos orientarte.
                        </p>
                    </div>
                </div>

                <form class="mt-7 space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <flux:field>
                            <flux:label for="name">Nombre</flux:label>
                            <flux:input id="name" name="name" autocomplete="name" placeholder="Tu nombre" disabled />
                        </flux:field>
                        <flux:field>
                            <flux:label for="email">Correo electrónico</flux:label>
                            <flux:input id="email" name="email" type="email" autocomplete="email" placeholder="tu@correo.com" disabled />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label for="subject">Asunto</flux:label>
                        <flux:input id="subject" name="subject" placeholder="¿Sobre qué necesitas ayuda?" disabled />
                    </flux:field>
                    <flux:field>
                        <flux:label for="message">Mensaje</flux:label>
                        <flux:textarea id="message" name="message" rows="5" placeholder="Escribe aquí tu mensaje..." disabled />
                    </flux:field>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">El envío de mensajes estará disponible próximamente.</p>
                        <flux:button type="button" variant="primary" disabled>Enviar mensaje</flux:button>
                    </div>
                </form>
            </flux:card>

            <div class="space-y-5 lg:col-span-2">
                <flux:card>
                    <flux:badge color="green" class="mb-4">¿Buscas un producto?</flux:badge>
                    <h2 class="text-lg font-semibold">Explora TechStore</h2>
                    <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                        Conoce más sobre nuestra propuesta y cómo hacemos más sencilla tu experiencia.
                    </p>
                    <flux:button class="mt-5" href="{{ route('about') }}" variant="ghost">Acerca de nosotros</flux:button>
                </flux:card>

                <flux:card class="bg-white dark:bg-[#0B1220]">
                    <flux:badge color="blue" class="mb-4">Tu cuenta</flux:badge>
                    <h2 class="text-lg font-semibold">Accede a tu espacio</h2>
                    <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                        Inicia sesión o crea una cuenta para continuar.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <flux:button href="{{ route('login') }}" variant="ghost">Iniciar sesión</flux:button>
                        <flux:button href="{{ route('register') }}" variant="primary">Crear cuenta</flux:button>
                    </div>
                </flux:card>
            </div>
        </section>
    </div>
</x-layouts::app>
