<?php

namespace Database\Seeders;

use App\Models\Procurement\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'Premium Oak Suppliers Inc.',
            'contact_person' => 'John Mitchell',
            'phone' => '09301234572',
            'email' => 'sales@oaksuppliers.com',
            'address' => '123 Wood Street, Forest City, WA 98765',
            'payment_terms' => 'Net 30',
            'status' => 'active',
            'total_orders' => 0,
            'total_spent' => 0,
        ]);

        Supplier::create([
            'name' => 'Maple Lumber Co.',
            'contact_person' => 'Sarah Johnson',
            'phone' => '09311234573',
            'email' => 'orders@maplelumber.com',
            'address' => '456 Tree Avenue, Timber Valley, OR 97123',
            'payment_terms' => 'Net 45',
            'status' => 'active',
            'total_orders' => 0,
            'total_spent' => 0,
        ]);

        Supplier::create([
            'name' => 'Exotic Woods Trading',
            'contact_person' => 'Carlos Rodriguez',
            'phone' => '09321234574',
            'email' => 'contact@exoticwoods.com',
            'address' => '789 Walnut Lane, Hardwood City, NC 28202',
            'payment_terms' => 'Net 15',
            'status' => 'active',
            'total_orders' => 0,
            'total_spent' => 0,
        ]);

        Supplier::create([
            'name' => 'Cedar & Pine Mills',
            'contact_person' => 'Emma Davis',
            'phone' => '09331234575',
            'email' => 'wholesale@cedarpinemills.com',
            'address' => '321 Lumber Road, Mill Town, GA 30303',
            'payment_terms' => 'Net 30',
            'status' => 'active',
            'total_orders' => 0,
            'total_spent' => 0,
        ]);

        $newSuppliers = [
            ['Feranz Salonga', '09171234501'],
            ['Kevin John Anga', '09182345602'],
            ['Marc Lester Bagal', '09193456703'],
            ['Yosh Batula', '09204567804'],
            ['Ed Lorenz Bersamin', '09215678905'],
            ['Stefan Jeremiah Birondo', '09226789006'],
            ['Jay Mark Burlado', '09237890107'],
            ['Johanna Jane Camus', '09248901208'],
            ['Joevan Capote', '09259012309'],
            ['Mheil Andrei Cenita', '09260123410'],
            ['Ryan Jay Compuesto', '09271234511'],
            ['Jean Claude Van Ejera', '09282345612'],
            ['Lloyd Justin Felecilda', '09293456713'],
            ['Crixa Gella', '09174567814'],
            ['Jay Lao', '09185678915'],
            ['Joemire Dave Loremas', '09196789016'],
            ['Emil Lian Jorge Macabenta', '09207890117'],
            ['Fe Anne Malasarte', '09218901218'],
            ['Fletcher Malazarte', '09229012319'],
            ['Neil Ian Mallari', '09230123420'],
            ['Gabriel Mantua', '09241234521'],
            ['Nathaniel Keene Merka', '09252345622'],
            ['Zenrick Denver Mintal', '09263456723'],
            ['Joren Montejo', '09274567824'],
            ['Earl Lawrence Obguia', '09285678925'],
            ['Jan Vincent Oclarit', '09296789026'],
            ['Matthew Oliver Opiano', '09177890127'],
            ['Airo Parondo', '09188901228'],
            ['Liea Maegan Picardal', '09199012329'],
            ['Tristhan Alfie Pintor', '09200123430'],
            ['Odyssey Sam Ragas', '09211234531'],
            ['Justin Troy Rosalada', '09222345632'],
            ['Kent Serencio', '09233456733'],
            ['Kent Leonel Sevellino', '09244567834'],
            ['Eulo Icon Sexcion', '09255678935'],
            ['Ryle Jade Tabay', '09266789036'],
            ['Earl Jonas Tigno', '09277890137'],
            ['Jomarie Travilla', '09288901238'],
            ['Clark Villanueva', '09299012339'],
            ['Archyl Ymbol', '09170123440'],
            ['Rolemir Kirk Don Zayasa', '09181234541'],
        ];

        foreach ($newSuppliers as $data) {
            $name = $data[0];
            $phone = $data[1];
            // Clean name for email
            $cleanName = strtolower(str_replace([' ', 'ñ'], ['.', 'n'], $name));
            
            Supplier::create([
                'name' => $name,
                'contact_person' => $name,
                'phone' => $phone,
                'email' => 'contact@' . $cleanName . '.com',
                'address' => 'Local Supplier Address',
                'payment_terms' => 'Net 30',
                'status' => 'active',
                'total_orders' => 0,
                'total_spent' => 0,
            ]);
        }
    }
}