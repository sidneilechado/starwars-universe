/**
 * Star Wars related utility functions and formatters
 */

const romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX'];

/**
 * Convert episode number to roman numerals
 */
export function formatEpisodeRoman(episodeId?: number): string {
    if (!episodeId) return '';
    return episodeId && episodeId <= romanNumerals.length
        ? romanNumerals[episodeId - 1]
        : episodeId.toString();
}

/**
 * Format release date to year only
 */
export function formatReleaseYear(releaseDate?: string): string {
    if (!releaseDate) return '';
    return new Date(releaseDate).getFullYear().toString();
}

/**
 * Format attribute values, handling unknown/n/a cases
 */
export function formatAttribute(value?: string): string {
    if (!value || value === 'unknown' || value === 'n/a') return 'Unknown';
    return value.split(',')[0].trim(); // Take first value if multiple
}

/**
 * Format height with units
 */
export function formatHeight(height?: string): string {
    if (!height || height === 'unknown' || height === 'n/a') return 'Unknown';
    return `${height} cm`;
}

/**
 * Format mass with units
 */
export function formatMass(mass?: string): string {
    if (!mass || mass === 'unknown' || mass === 'n/a') return 'Unknown';
    return `${mass} kg`;
}

/**
 * Get gender color classes for badges
 */
export function getGenderColor(gender?: string): string {
    if (!gender) return 'bg-gray-100 text-gray-800';
    const normalizedGender = gender.toLowerCase();
    if (normalizedGender === 'male') return 'bg-blue-100 text-blue-800';
    if (normalizedGender === 'female') return 'bg-pink-100 text-pink-800';
    return 'bg-gray-100 text-gray-800';
}

/**
 * Generate initials from a name
 */
export function generateInitials(name?: string): string {
    if (!name) return '??';
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();
}

/**
 * Parse and format producers list
 */
export function formatProducers(producerString?: string): string[] {
    if (!producerString) return [];
    return producerString.split(',').map((p) => p.trim());
}

/**
 * Truncate opening crawl text
 */
export function truncateText(text: string, maxLength: number = 150): string {
    if (!text) return '';
    return text.length > maxLength
        ? text.substring(0, maxLength) + '...'
        : text;
}
