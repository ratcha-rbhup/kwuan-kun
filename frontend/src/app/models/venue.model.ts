/**
 * =============================================================================
 * Badminton Court & Venue Preset Models
 * =============================================================================
 */

export interface VenuePreset {
  id: string;
  name: string;
  lat: number;
  lng: number;
  address: string;
  province: string;
  district: string;
}

export interface BadmintonCourtVenue {
  id: number;
  name: string;
  isPrimary: boolean;
  lat: number;
  lng: number;
  address: string;
  district: string;
  province: string;
  landmark: string;
  courtsCount: string;
  floorType: string;
  regularSchedule: string;
  facilities: string[];
  googleMapsUrl: string;
  appleMapsUrl: string;
  distance: string;
}
