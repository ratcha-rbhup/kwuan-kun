/**
 * =============================================================================
 * Add Kuan (แอดก๊วน) - Global Mock Data Configuration
 * Central master toggle to enable, disable, or hide all mock data easily
 * =============================================================================
 */

export const MOCK_CONFIG = {
  /**
   * Master toggle:
   * - true: Use mock data across the application for UI development/testing.
   * - false: Hide mock data or return empty lists / fallback to real backend API.
   */
  enabled: true,

  /**
   * Simulated network delay in milliseconds for testing loading states (if needed)
   */
  simulatedDelayMs: 200
};

/**
 * Universal helper function to resolve mock data according to MOCK_CONFIG.enabled.
 * When enabled is false, it returns the provided fallback (e.g. empty array or null).
 *
 * @example
 * parties = signal(getMockData(MOCK_PARTIES, []));
 */
export function getMockData<T>(mockData: T, fallback: T): T {
  return MOCK_CONFIG.enabled ? mockData : fallback;
}
