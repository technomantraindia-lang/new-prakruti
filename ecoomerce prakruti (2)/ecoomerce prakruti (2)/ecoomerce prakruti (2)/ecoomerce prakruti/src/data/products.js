// Import specific product images
import masoorDalImg from '../assets/images/bestseller_masoor_dal.jpg';
import panchratnaDalImg from '../assets/images/panchratna_dal.png';
import chiaSeedsImg from '../assets/images/chia_seeds.png';
import girGheeImg from '../assets/images/ghee.png';
import basmatiRiceImg from '../assets/images/bestseller_basmati_rice.jpg';
import turmericPowderImg from '../assets/images/turmeric_powder.png';
import jeerakaImg from '../assets/images/bestseller_jeeraka.jpg';
import bengaluGunduImg from '../assets/images/bestseller_bengalu_gundu.jpg';
import tuarDalImg from '../assets/images/bestseller_tuar_dal.jpg';

// Import category images as fallbacks
import cerealPulsesImg from '../assets/images/cereal_pulses.png';
import healthySeedsImg from '../assets/images/healthy_seeds.png';
import indianSpicesImg from '../assets/images/indian_spices.png';
import milletsImg from '../assets/images/milltet.png';
import oilsGheeImg from '../assets/images/oil and ghe.png';
import riceFlourImg from '../assets/images/rice flour.png';
import sweetenersImg from '../assets/images/sweeterns.png';

export const categoriesData = [
  {
    id: 'cereal-pulses',
    name: 'Cereal & Pulses',
    count: 9,
    description: 'Daily essentials that nourish your body with pure plant-based protein and fiber.',
    image: cerealPulsesImg,
  },
  {
    id: 'healthy-seeds',
    name: 'Healthy Seeds',
    count: 4,
    description: 'Tiny powerhouses packed with nutrients for a healthier you.',
    image: healthySeedsImg,
  },
  {
    id: 'indian-spices',
    name: 'Indian Spices',
    count: 12,
    description: 'Aromatic spices that add flavor, warmth and wellness to every meal.',
    image: indianSpicesImg,
  },
  {
    id: 'millets',
    name: 'Millets',
    count: 6,
    description: 'Ancient grains for modern lifestyles - wholesome, hearty and naturally gluten-free.',
    image: milletsImg,
  },
  {
    id: 'oils-ghee',
    name: 'Oils & Ghee',
    count: 5,
    description: 'Pure, cold-pressed oils and traditional ghee for a healthy you.',
    image: oilsGheeImg,
  },
  {
    id: 'rice-flours',
    name: 'Rice & Flours',
    count: 9,
    description: 'Wholesome grains and flours for everyday cooking and baking.',
    image: riceFlourImg,
  },
  {
    id: 'sweeteners',
    name: 'Sweeteners',
    count: 3,
    description: 'Natural sweeteners for mindful indulgence.',
    image: sweetenersImg,
  }
];

const productsDataRaw = [
  // --- CEREAL & PULSES (9 items) ---
  {
    id: 1,
    name: 'Masoor Dal / Pink Lentil Split',
    category: 'Cereal & Pulses',
    price: 120,
    image: masoorDalImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber', 'Gut Friendly']
  },
  {
    id: 2,
    name: 'Panchratna Dal/Mix Dal ( 5 Pulses )',
    category: 'Cereal & Pulses',
    price: 140,
    image: panchratnaDalImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber']
  },
  {
    id: 3,
    name: 'Rajma Jammu',
    category: 'Cereal & Pulses',
    price: 150,
    image: cerealPulsesImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber']
  },
  {
    id: 4,
    name: 'Split Bengal Gram (Chana Dal)',
    category: 'Cereal & Pulses',
    price: 110,
    image: cerealPulsesImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Gut Friendly']
  },
  {
    id: 5,
    name: 'Tuar Dal/Tur/Arhar',
    category: 'Cereal & Pulses',
    price: 120,
    image: tuarDalImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein']
  },
  {
    id: 6,
    name: 'Urad Dal Chilka / Black Gram Split',
    category: 'Cereal & Pulses',
    price: 130,
    image: cerealPulsesImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber']
  },
  {
    id: 7,
    name: 'White Chickpeas (Kabuli) Dollar Big Size',
    category: 'Cereal & Pulses',
    price: 160,
    image: cerealPulsesImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber']
  },
  {
    id: 8,
    name: 'Green Gram Whole (Moong)',
    category: 'Cereal & Pulses',
    price: 120,
    image: cerealPulsesImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber', 'Immunity Booster']
  },
  {
    id: 9,
    name: 'Brown Chick Peas Small Size [Desi Chana]',
    category: 'Cereal & Pulses',
    price: 110,
    image: cerealPulsesImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber']
  },

  // --- HEALTHY SEEDS (4 items) ---
  {
    id: 10,
    name: 'Chia Seed',
    category: 'Healthy Seeds',
    price: 180,
    image: chiaSeedsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Heart Healthy', 'Immunity Booster']
  },
  {
    id: 11,
    name: 'Flax Seed',
    category: 'Healthy Seeds',
    price: 110,
    image: healthySeedsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Heart Healthy']
  },
  {
    id: 12,
    name: 'Pumpkin Seeds',
    category: 'Healthy Seeds',
    price: 170,
    image: healthySeedsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Immunity Booster', 'Heart Healthy']
  },
  {
    id: 13,
    name: 'Sunflower Seed',
    category: 'Healthy Seeds',
    price: 120,
    image: healthySeedsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Immunity Booster', 'Heart Healthy']
  },

  // --- INDIAN SPICES (12 items) ---
  {
    id: 14,
    name: 'Ajwain / Carom Seed',
    category: 'Indian Spices',
    price: 110,
    image: indianSpicesImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly']
  },
  {
    id: 15,
    name: 'Black Pepper Whole / Kali Mirch',
    category: 'Indian Spices',
    price: 130,
    image: indianSpicesImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly', 'Immunity Booster']
  },
  {
    id: 16,
    name: 'Black Sesame (Til)',
    category: 'Indian Spices',
    price: 110,
    image: indianSpicesImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Heart Healthy']
  },
  {
    id: 17,
    name: 'Cinnamon Stick / Dal Chini',
    category: 'Indian Spices',
    price: 110,
    image: indianSpicesImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly', 'Immunity Booster']
  },
  {
    id: 18,
    name: 'Clove Whole / Loung',
    category: 'Indian Spices',
    price: 120,
    image: indianSpicesImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Immunity Booster']
  },
  {
    id: 19,
    name: 'Coriander Powder',
    category: 'Indian Spices',
    price: 110,
    image: indianSpicesImg,
    types: ['Organic', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly']
  },
  {
    id: 20,
    name: 'Cumin (Jeera) / Cumin Whole',
    category: 'Indian Spices',
    price: 110,
    image: indianSpicesImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly']
  },
  {
    id: 21,
    name: 'Cumin Powder / Jeera Powder',
    category: 'Indian Spices',
    price: 110,
    image: indianSpicesImg,
    types: ['Organic', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly']
  },
  {
    id: 22,
    name: 'Pink Rock Salt / Sendha Namak (Light Pink)',
    category: 'Indian Spices',
    price: 55,
    image: indianSpicesImg,
    types: ['Organic', 'Unrefined', 'No Preservatives'],
    wellness: ['Gut Friendly']
  },
  {
    id: 23,
    name: 'Red Chilli Powder',
    category: 'Indian Spices',
    price: 110,
    image: indianSpicesImg,
    types: ['Organic', 'Vegan', 'No Preservatives'],
    wellness: ['Immunity Booster']
  },
  {
    id: 24,
    name: 'Turmeric Powder',
    category: 'Indian Spices',
    price: 110,
    image: turmericPowderImg,
    types: ['Organic', 'Vegan', 'No Preservatives'],
    wellness: ['Immunity Booster', 'Gut Friendly']
  },
  {
    id: 25,
    name: 'White Sesame (Til) / Natural',
    category: 'Indian Spices',
    price: 110,
    image: indianSpicesImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Heart Healthy']
  },

  // --- MILLETS (6 items) ---
  {
    id: 26,
    name: 'Bajra Whole / Pearl Millet',
    category: 'Millets',
    price: 75,
    image: milletsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Heart Healthy']
  },
  {
    id: 27,
    name: 'Finger (Ragi) Millet',
    category: 'Millets',
    price: 75,
    image: milletsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Gut Friendly', 'High in Protein']
  },
  {
    id: 28,
    name: 'Jawar/Sorghum Whole',
    category: 'Millets',
    price: 85,
    image: milletsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Heart Healthy']
  },
  {
    id: 29,
    name: 'Kodo (Kodro) Millet',
    category: 'Millets',
    price: 80,
    image: milletsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Gut Friendly']
  },
  {
    id: 30,
    name: 'Little Millet',
    category: 'Millets',
    price: 80,
    image: milletsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Heart Healthy']
  },
  {
    id: 31,
    name: 'White Quinoa Seeds (Processed)',
    category: 'Millets',
    price: 120,
    image: milletsImg,
    types: ['Organic', 'Gluten-Free', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber', 'Heart Healthy']
  },

  // --- OILS & GHEE (5 items) ---
  {
    id: 32,
    name: 'A2 Gir Cow Ghee Bilona',
    category: 'Oils & Ghee',
    price: 750,
    image: girGheeImg,
    types: ['Organic', 'Unrefined', 'Whole', 'No Preservatives'],
    wellness: ['Gut Friendly', 'Immunity Booster']
  },
  {
    id: 33,
    name: 'Black Sesame Oil (Cold Pressed)',
    category: 'Oils & Ghee',
    price: 290,
    image: oilsGheeImg,
    types: ['Organic', 'Cold Pressed', 'Unrefined', 'No Preservatives'],
    wellness: ['Heart Healthy']
  },
  {
    id: 34,
    name: 'Groundnut Oil ( Cold Pressed)',
    category: 'Oils & Ghee',
    price: 220,
    image: oilsGheeImg,
    types: ['Organic', 'Cold Pressed', 'Unrefined', 'No Preservatives'],
    wellness: ['Heart Healthy']
  },
  {
    id: 35,
    name: 'Sunflower Oil [Cold Pressed]',
    category: 'Oils & Ghee',
    price: 180,
    image: oilsGheeImg,
    types: ['Organic', 'Cold Pressed', 'Unrefined', 'No Preservatives'],
    wellness: ['Heart Healthy']
  },
  {
    id: 36,
    name: 'Olive Oil Extra Virgin',
    category: 'Oils & Ghee',
    price: 650,
    image: oilsGheeImg,
    types: ['Cold Pressed', 'Unrefined', 'Vegan', 'No Preservatives'],
    wellness: ['Heart Healthy', 'Immunity Booster']
  },

  // --- RICE & FLOURS (9 items) ---
  {
    id: 37,
    name: 'Bajra Atta / Pearl Millet Flour',
    category: 'Rice & Flours',
    price: 75,
    image: riceFlourImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Heart Healthy']
  },
  {
    id: 38,
    name: 'Basmati Rice Brown',
    category: 'Rice & Flours',
    price: 150,
    image: basmatiRiceImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Heart Healthy']
  },
  {
    id: 39,
    name: 'Basmati Rice White',
    category: 'Rice & Flours',
    price: 130,
    image: basmatiRiceImg,
    types: ['Organic', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly']
  },
  {
    id: 40,
    name: 'Sona Masoori - White Rice',
    category: 'Rice & Flours',
    price: 120,
    image: riceFlourImg,
    types: ['Organic', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly']
  },
  {
    id: 41,
    name: 'Chana Besan / Bengal Gram Flour',
    category: 'Rice & Flours',
    price: 90,
    image: riceFlourImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Gut Friendly']
  },
  {
    id: 42,
    name: 'Finger (Ragi) Millet Flour',
    category: 'Rice & Flours',
    price: 80,
    image: riceFlourImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Gut Friendly', 'High in Protein']
  },
  {
    id: 43,
    name: 'Jowar Atta/Sorghum Flour',
    category: 'Rice & Flours',
    price: 75,
    image: riceFlourImg,
    types: ['Organic', 'Gluten-Free', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber', 'Heart Healthy']
  },
  {
    id: 44,
    name: 'Wheat Flour',
    category: 'Rice & Flours',
    price: 60,
    image: riceFlourImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Rich in Fiber']
  },
  {
    id: 45,
    name: 'Multi Grain Flour',
    category: 'Rice & Flours',
    price: 100,
    image: riceFlourImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber', 'Heart Healthy']
  },

  // --- SWEETENERS (3 items) ---
  {
    id: 46,
    name: 'Jaggery Powder',
    category: 'Sweeteners',
    price: 110,
    image: sweetenersImg,
    types: ['Organic', 'Unrefined', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly', 'Immunity Booster']
  },
  {
    id: 47,
    name: 'Off White Sugar Light',
    category: 'Sweeteners',
    price: 90,
    image: sweetenersImg,
    types: ['Organic', 'Vegan', 'No Preservatives'],
    wellness: []
  },
  {
    id: 48,
    name: 'Raw Sugar / Khandasari Sugar (Brown)',
    category: 'Sweeteners',
    price: 110,
    image: sweetenersImg,
    types: ['Organic', 'Unrefined', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly']
  },
  {
    id: 50,
    name: 'Pilipili Jeeraka (200g)',
    category: 'Indian Spices',
    price: 150,
    originalPrice: 200,
    image: jeerakaImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['Gut Friendly', 'Immunity Booster']
  },
  {
    id: 51,
    name: 'Ralii Bengalu Gundu (2kg)',
    category: 'Cereal & Pulses',
    price: 110,
    originalPrice: 160,
    image: bengaluGunduImg,
    types: ['Organic', 'Whole', 'Vegan', 'No Preservatives'],
    wellness: ['High in Protein', 'Rich in Fiber']
  }
];

// Keep each product's own catalog image for the offline/fallback storefront.
export const productsData = productsDataRaw;
