/**
 * Star Wars API related types and interfaces
 */

/**
 * Represents a Film from the Star Wars API
 */
export interface Film {
    uid: string;
    properties: {
        title: string;
        episode_id: number;
        director: string;
        producer: string;
        release_date: string;
        opening_crawl: string;
        characters: string[];
    };
}

/**
 * Represents a Character from the Star Wars API
 */
export interface Character {
    uid: string;
    properties: {
        name: string;
        height: string;
        mass: string;
        hair_color: string;
        skin_color: string;
        eye_color: string;
        birth_year: string;
        gender: string;
        films: string[];
    };
}

/**
 * Represents a top search query statistic
 */
export interface TopQuery {
    query: string;
    type: string;
    count: number;
    percentage: number;
}

/**
 * Represents hourly distribution data for statistics
 */
export interface HourlyData {
    hour: number;
    count: number;
    percentage: number;
}

/**
 * Represents response time percentiles
 */
export interface ResponseTimePercentiles {
    p50: number;
    p90: number;
    p95: number;
    p99: number;
}

/**
 * Represents slow query analysis
 */
export interface SlowQueries {
    count: number;
    percentage: number;
    threshold_ms: number;
    slowest_query: {
        query: string;
        type: string;
        response_time_ms: number;
    } | null;
}

/**
 * Represents search quality metrics
 */
export interface SearchQuality {
    zero_result_searches: {
        count: number;
        percentage: number;
    };
    effectiveness_ratio: number;
    results_distribution: {
        range: string;
        count: number;
        percentage: number;
    }[];
    average_results_per_search: number;
}

/**
 * Represents user behavior analytics
 */
export interface UserBehavior {
    device_types: {
        device: string;
        count: number;
        percentage: number;
    }[];
    browsers: {
        browser: string;
        count: number;
        percentage: number;
    }[];
    search_type_distribution: {
        type: string;
        count: number;
        percentage: number;
    }[];
}

/**
 * Represents geographic insights
 */
export interface GeographicInsights {
    top_countries: {
        country: string;
        count: number;
        percentage: number;
        unique_ips: number;
    }[];
    unique_ips: number;
    international_percentage: number;
}

/**
 * Represents content analysis
 */
export interface ContentAnalysis {
    query_length_distribution: {
        range: string;
        count: number;
        percentage: number;
    }[];
    average_query_length: number;
    most_common_words: {
        word: string;
        count: number;
        percentage: number;
    }[];
    search_patterns: {
        contains_quotes: {
            count: number;
            percentage: number;
        };
        single_character: {
            count: number;
            percentage: number;
        };
        numeric_queries: {
            count: number;
            percentage: number;
        };
        very_long_queries: {
            count: number;
            percentage: number;
        };
    };
}

/**
 * Represents the complete statistics data structure
 */
export interface StatisticsData {
    top_queries: TopQuery[];
    average_response_time_ms: number;
    response_time_percentiles: ResponseTimePercentiles;
    slow_queries: SlowQueries;
    search_quality: SearchQuality;
    user_behavior: UserBehavior;
    geographic_insights: GeographicInsights;
    content_analysis: ContentAnalysis;
    hourly_distribution: HourlyData[];
    total_searches: number;
    computed_at: string | null;
}
