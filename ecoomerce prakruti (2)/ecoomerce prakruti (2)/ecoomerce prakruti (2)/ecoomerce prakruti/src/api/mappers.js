import { categoriesData, productsData } from '../data/products';
import packgringImg from '../assets/images/new packing .png';

function normalizeName(value) {
  return String(value || '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, ' ')
    .trim();
}

function findLocalProduct(product) {
  const name = normalizeName(product?.name);
  const slug = product?.slug;

  return (
    productsData.find((item) => item.slug === slug) ||
    productsData.find((item) => normalizeName(item.name) === name) ||
    productsData.find((item) => {
      const localName = normalizeName(item.name);
      return name && localName && (name.includes(localName) || localName.includes(name));
    })
  );
}

export function resolveCategoryName(category) {
  if (!category) return '';
  if (typeof category === 'string') return category;
  return category.name || '';
}

export function normalizeProduct(product) {
  if (!product) return null;

  const local = findLocalProduct(product) || {};
  const category = resolveCategoryName(product.category) || local.category || '';
  const price = Number(product.price ?? local.price ?? 0);
  const regular = Number(product.regular_price ?? local.price ?? price);
  const variations = Array.isArray(product.variations)
    ? product.variations.map((variation) => ({
        ...variation,
        id: variation.id,
        label: variation.label || variation.package_size || variation.attributes?.Weight || variation.weight || 'Pack',
        price: Number(variation.price ?? 0),
        sale_price: variation.sale_price !== null && variation.sale_price !== undefined ? Number(variation.sale_price) : null,
        available_stock: Number(variation.available_stock ?? 0),
      }))
    : (local.variations || []);
  const primaryImage = product.image || local.image || packgringImg;
  const images = [
    primaryImage,
    ...(Array.isArray(product.images) ? product.images : []),
  ].filter(Boolean).filter((image, index, list) => list.indexOf(image) === index);

  return {
    ...local,
    ...product,
    id: product.id,
    slug: product.slug || local.slug,
    name: product.name || local.name,
    category,
    price,
    regular_price: regular,
    originalPrice: regular > price ? regular : null,
    image: primaryImage,
    images,
    types: Array.isArray(product.types) && product.types.length
      ? product.types
      : (local.types || ['Organic', 'Natural']),
    wellness: product.wellness || local.wellness || [],
    short_description: product.short_description || product.short_desc || local.short_description || '',
    description: product.description || local.description || '',
    nutritional_info: product.nutritional_info || local.nutritional_info || '',
    product_information: product.product_information || local.product_information || {},
    weight: product.weight ? String(product.weight) : (local.weight || ''),
    variations,
  };
}

export function normalizeCategory(category) {
  if (!category) return null;

  const local = categoriesData.find((item) =>
    item.id === category.slug ||
    item.name.toLowerCase() === String(category.name || '').toLowerCase()
  );

  return {
    id: category.slug || category.id || local?.id,
    numericId: category.id,
    name: category.name || local?.name,
    slug: category.slug || local?.id,
    count: category.count ?? local?.count ?? 0,
    description: category.description || local?.description || '',
    image: category.image || local?.image || packgringImg,
  };
}

export function normalizeProductList(list) {
  return (Array.isArray(list) ? list : []).map(normalizeProduct).filter(Boolean);
}

export function normalizeCategoryList(list) {
  return (Array.isArray(list) ? list : []).map(normalizeCategory).filter(Boolean);
}
