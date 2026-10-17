<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Computación' => 'Laptops y equipos para trabajo y estudio.',
            'Audio' => 'Audífonos, bocinas y accesorios de audio.',
            'Accesorios' => 'Accesorios prácticos para tus dispositivos.',
            'Almacenamiento' => 'Opciones para guardar y transportar tus archivos.',
            'Componentes' => 'Componentes para armar y actualizar tu computadora.',
        ];

        $categoryIds = [];

        foreach ($categories as $name => $description) {
            $category = Category::withTrashed()->firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'status' => true],
            );

            if ($category->trashed()) {
                $category->restore();
            }

            $categoryIds[$name] = $category->id;
        }

        $products = [
            ['category' => 'Computación', 'name' => 'Laptop Pro 14', 'slug' => 'laptop-pro-14', 'description' => 'Laptop ligera para trabajo, estudio y tareas creativas.', 'price' => '18499.00', 'stock' => 12, 'featured' => true],
            ['category' => 'Computación', 'name' => 'Laptop Esencial 15', 'slug' => 'laptop-esencial-15', 'description' => 'Equipo versátil para productividad y uso diario.', 'price' => '12999.00', 'stock' => 9, 'featured' => false],
            ['category' => 'Computación', 'name' => 'Monitor 27 pulgadas', 'slug' => 'monitor-27-pulgadas', 'description' => 'Monitor amplio para trabajar cómodamente con varias ventanas.', 'price' => '5499.00', 'stock' => 14, 'featured' => true],
            ['category' => 'Audio', 'name' => 'Audífonos inalámbricos', 'slug' => 'audifonos-inalambricos-demo', 'description' => 'Audífonos inalámbricos para música, llamadas y viajes.', 'price' => '1799.00', 'stock' => 25, 'featured' => true],
            ['category' => 'Audio', 'name' => 'Bocina Bluetooth', 'slug' => 'bocina-bluetooth', 'description' => 'Bocina portátil para disfrutar audio en cualquier lugar.', 'price' => '1199.00', 'stock' => 18, 'featured' => false],
            ['category' => 'Audio', 'name' => 'Micrófono USB', 'slug' => 'microfono-usb', 'description' => 'Micrófono USB para reuniones, clases y transmisiones.', 'price' => '1499.00', 'stock' => 11, 'featured' => false],
            ['category' => 'Accesorios', 'name' => 'Teclado mecánico', 'slug' => 'teclado-mecanico-demo', 'description' => 'Teclado mecánico cómodo para escribir y jugar.', 'price' => '1399.00', 'stock' => 20, 'featured' => true],
            ['category' => 'Accesorios', 'name' => 'Mouse inalámbrico', 'slug' => 'mouse-inalambrico-demo', 'description' => 'Mouse inalámbrico compacto para escritorio o movilidad.', 'price' => '549.00', 'stock' => 30, 'featured' => false],
            ['category' => 'Accesorios', 'name' => 'Base para laptop', 'slug' => 'base-para-laptop', 'description' => 'Base ajustable para mejorar la postura al trabajar.', 'price' => '799.00', 'stock' => 16, 'featured' => false],
            ['category' => 'Almacenamiento', 'name' => 'SSD externo 1 TB', 'slug' => 'ssd-externo-1tb', 'description' => 'Unidad externa rápida para respaldos y archivos grandes.', 'price' => '2199.00', 'stock' => 13, 'featured' => true],
            ['category' => 'Almacenamiento', 'name' => 'Memoria USB 128 GB', 'slug' => 'memoria-usb-128gb', 'description' => 'Memoria portátil para documentos y archivos del día a día.', 'price' => '299.00', 'stock' => 40, 'featured' => false],
            ['category' => 'Almacenamiento', 'name' => 'Disco duro externo 2 TB', 'slug' => 'disco-duro-externo-2tb', 'description' => 'Espacio adicional para respaldar fotos, videos y proyectos.', 'price' => '1699.00', 'stock' => 10, 'featured' => false],
        ];

        $additionalProducts = [
            'Computación' => [
                'Laptop Gaming 16' => '24999.00',
                'Mini PC Pro' => '8999.00',
                'Monitor UltraWide 34' => '9999.00',
                'Monitor portátil 15' => '3999.00',
                'Teclado para laptop USB' => '449.00',
                'Webcam Full HD' => '899.00',
                'Webcam 4K' => '2199.00',
                'Docking station USB-C' => '2699.00',
                'Hub USB-C multipuerto' => '899.00',
                'Soporte para monitor' => '1199.00',
            ],
            'Audio' => [
                'Audífonos gamer' => '1599.00',
                'Audífonos de diadema' => '999.00',
                'Audífonos deportivos' => '699.00',
                'Bocina inteligente' => '1899.00',
                'Bocina portátil compacta' => '799.00',
                'Barra de sonido' => '3299.00',
                'Micrófono de condensador' => '2499.00',
                'Interfaz de audio USB' => '2799.00',
                'Adaptador Bluetooth para audio' => '399.00',
                'Soporte para audífonos' => '349.00',
            ],
            'Accesorios' => [
                'Teclado inalámbrico compacto' => '799.00',
                'Mouse gamer RGB' => '1099.00',
                'Mouse ergonómico' => '899.00',
                'Tapete para mouse extendido' => '499.00',
                'Base refrigerante para laptop' => '999.00',
                'Cargador USB-C 65 W' => '849.00',
                'Cargador USB-C 100 W' => '1299.00',
                'Cable USB-C reforzado' => '249.00',
                'Adaptador USB-C a HDMI' => '499.00',
            ],
            'Almacenamiento' => [
                'SSD externo 500 GB' => '1399.00',
                'SSD interno NVMe 1 TB' => '1899.00',
                'SSD interno NVMe 2 TB' => '3299.00',
                'Disco duro externo 1 TB' => '1199.00',
                'Memoria USB 64 GB' => '179.00',
                'Memoria USB 256 GB' => '499.00',
                'Tarjeta microSD 128 GB' => '329.00',
                'Tarjeta microSD 256 GB' => '649.00',
                'Carcasa para SSD externo' => '599.00',
            ],
        ];

        $componentProducts = [
            ['name' => 'Tarjeta gráfica GeForce RTX 4060 8 GB', 'description' => 'GPU con 8 GB GDDR6, arquitectura Ada y soporte para trazado de rayos y DLSS 3.', 'price' => '7499.00'],
            ['name' => 'Tarjeta gráfica Radeon RX 7600 8 GB', 'description' => 'GPU con 8 GB GDDR6, interfaz PCIe 4.0 y soporte para tecnologías AMD FidelityFX.', 'price' => '6899.00'],
            ['name' => 'Memoria RAM DDR5 16 GB 5600 MHz', 'description' => 'Módulo DDR5 de 16 GB a 5600 MHz para equipos compatibles con memoria DDR5.', 'price' => '999.00'],
            ['name' => 'Kit de memoria RAM DDR5 32 GB 6000 MHz', 'description' => 'Kit de 2 módulos de 16 GB DDR5 a 6000 MHz para configuración de doble canal.', 'price' => '2199.00'],
            ['name' => 'Memoria RAM DDR4 16 GB 3200 MHz', 'description' => 'Módulo DDR4 de 16 GB a 3200 MHz para computadoras compatibles con DDR4.', 'price' => '699.00'],
            ['name' => 'Procesador Ryzen 5 7600', 'description' => 'Procesador de 6 núcleos y 12 hilos, socket AM5 y gráficos integrados Radeon.', 'price' => '3799.00'],
            ['name' => 'Procesador Core i5-14400F', 'description' => 'Procesador de 10 núcleos (6 de rendimiento y 4 eficientes), 16 hilos y socket LGA1700. Requiere tarjeta gráfica dedicada.', 'price' => '4199.00'],
            ['name' => 'Tarjeta madre B650M Wi-Fi', 'description' => 'Placa madre micro-ATX con socket AM5, soporte para DDR5, ranura M.2 NVMe y Wi-Fi integrado.', 'price' => '3299.00'],
            ['name' => 'Tarjeta madre B760M DDR5', 'description' => 'Placa madre micro-ATX con socket LGA1700, soporte para DDR5 y almacenamiento M.2 NVMe.', 'price' => '2899.00'],
            ['name' => 'Fuente de poder 750 W 80 Plus Gold', 'description' => 'Fuente de 750 W con certificación 80 Plus Gold y cableado modular para equipos de alto rendimiento.', 'price' => '2199.00'],
            ['name' => 'Gabinete ATX con frente mesh', 'description' => 'Gabinete ATX con panel frontal mesh, panel lateral de vidrio y espacio para tarjetas gráficas de gran tamaño.', 'price' => '1599.00'],
            ['name' => 'Enfriamiento líquido 240 mm', 'description' => 'Sistema de enfriamiento líquido AIO con radiador de 240 mm y ventiladores PWM. Verifica compatibilidad del socket.', 'price' => '1899.00'],
        ];

        $additionalProductNumber = count($products) + 1;

        foreach ($additionalProducts as $category => $categoryProducts) {
            foreach ($categoryProducts as $name => $price) {
                $products[] = [
                    'category' => $category,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => 'Producto de '.$category.' para complementar tu equipo y facilitar tus actividades diarias.',
                    'price' => $price,
                    'stock' => ($additionalProductNumber * 7) % 35 + 1,
                    'featured' => $additionalProductNumber % 5 === 0,
                ];

                $additionalProductNumber++;
            }
        }

        foreach ($componentProducts as $componentProduct) {
            $products[] = [
                'category' => 'Componentes',
                'name' => $componentProduct['name'],
                'slug' => Str::slug($componentProduct['name']),
                'description' => $componentProduct['description'],
                'price' => $componentProduct['price'],
                'stock' => ($additionalProductNumber * 7) % 35 + 1,
                'featured' => $additionalProductNumber % 5 === 0,
            ];

            $additionalProductNumber++;
        }

        foreach ($products as $productData) {
            $product = Product::withTrashed()->firstOrCreate(
                ['slug' => $productData['slug']],
                [
                    'category_id' => $categoryIds[$productData['category']],
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'featured' => $productData['featured'],
                    'status' => true,
                ],
            );

            if ($product->trashed()) {
                $product->restore();
            }
        }
    }
}
