import {
  Component,
  Input,
  Output,
  EventEmitter,
  AfterViewInit,
  OnDestroy,
  ElementRef,
  ViewChild,
  signal
} from '@angular/core';

declare const L: any;

@Component({
  selector: 'app-court-map-picker',
  standalone: false,
  templateUrl: './court-map-picker.component.html',
  styleUrls: ['./court-map-picker.component.scss']
})
export class CourtMapPickerComponent implements AfterViewInit, OnDestroy {
  @ViewChild('mapContainer', { static: false }) mapContainer!: ElementRef<HTMLDivElement>;

  @Input() lat: number = 13.7225; // Default: Bangkok (Tobacco Court / Rama 4)
  @Input() lng: number = 100.5574;
  @Input() courtName: string = 'สนามแบดมินตัน';
  @Input() address: string = '';
  @Input() mode: 'picker' | 'view' = 'picker'; // 'picker' = draggable map with center pin; 'view' = marker view
  @Input() height: string = '240px';
  @Input() showRouteButtons: boolean = true;
  @Input() showCoordinateBadge: boolean = true;

  @Output() coordinatesChange = new EventEmitter<{ lat: number; lng: number; googleMapsUrl: string }>();

  isMapExpanded = signal(false);
  isDragging = signal(false);
  isLocating = signal(false);
  currentLat = signal(13.7225);
  currentLng = signal(100.5574);

  private leafletMap: any;
  private viewMarker: any;
  private mapId: string = 'map-' + Math.random().toString(36).substring(2, 9);

  ngAfterViewInit(): void {
    this.currentLat.set(this.lat);
    this.currentLng.set(this.lng);
    // Slight delay to ensure DOM sizing is rendered
    setTimeout(() => {
      this.initMap();
    }, 150);
  }

  ngOnDestroy(): void {
    if (this.leafletMap) {
      this.leafletMap.remove();
      this.leafletMap = null;
    }
  }

  get mapElementId(): string {
    return this.mapId;
  }

  get googleMapsUrl(): string {
    return `https://www.google.com/maps/search/?api=1&query=${this.currentLat()},${this.currentLng()}`;
  }

  get appleMapsUrl(): string {
    return `https://maps.apple.com/?q=${this.currentLat()},${this.currentLng()}`;
  }

  private initMap(): void {
    if (typeof L === 'undefined') {
      console.warn('Leaflet (L) is not loaded yet');
      return;
    }

    const container = this.mapContainer?.nativeElement;
    if (!container) return;

    // Center coordinates
    const centerCoords = [this.currentLat(), this.currentLng()];

    // Initialize Leaflet Map
    this.leafletMap = L.map(container, {
      center: centerCoords,
      zoom: 16,
      zoomControl: false,
      attributionControl: false
    });

    // Add Free OpenStreetMap Tile Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(this.leafletMap);

    // Zoom control at bottom right
    L.control.zoom({ position: 'bottomright' }).addTo(this.leafletMap);

    if (this.mode === 'picker') {
      // Movestart -> bounce pin up
      this.leafletMap.on('movestart', () => {
        this.isDragging.set(true);
      });

      // Moveend -> pin drops, update coordinates
      this.leafletMap.on('moveend', () => {
        this.isDragging.set(false);
        const center = this.leafletMap.getCenter();
        if (center) {
          const lat = Number(center.lat.toFixed(6));
          const lng = Number(center.lng.toFixed(6));
          this.currentLat.set(lat);
          this.currentLng.set(lng);
          this.emitCoordinates(lat, lng);
        }
      });
    } else {
      // View Mode: Place a branded Court Marker with Shuttlecock SVG
      const courtIcon = L.divIcon({
        className: 'custom-court-leaflet-icon',
        html: `
          <div class="court-marker-pin">
            <svg class="shuttlecock-svg" viewBox="0 0 24 24" width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Shuttlecock Cork Dome Base -->
              <path d="M9.5 15.2C9.5 18 10.6 20.2 12 20.2C13.4 20.2 14.5 18 14.5 15.2C14.5 14.2 13.4 13.8 12 13.8C10.6 13.8 9.5 14.2 9.5 15.2Z" fill="#FFFFFF"/>
              <!-- Feathers Cone & Ribs -->
              <path d="M5.8 4L9.5 14.2M18.2 4L14.5 14.2M12 3.8V14.2M8.8 4.2L10.5 14.2M15.2 4.2L13.5 14.2" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
              <!-- Feathers Top Crown Rim -->
              <path d="M5.8 4C8 2.8 16 2.8 18.2 4" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
              <!-- Double Binding Thread Bands -->
              <path d="M7.2 7.6C8.8 8.3 15.2 8.3 16.8 7.6" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"/>
              <path d="M8.2 11.2C9.6 11.8 14.4 11.8 15.8 11.2" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
          </div>
        `,
        iconSize: [42, 42],
        iconAnchor: [21, 42],
        popupAnchor: [0, -40]
      });

      this.viewMarker = L.marker(centerCoords, { icon: courtIcon }).addTo(this.leafletMap);
      if (this.courtName) {
        this.viewMarker.bindPopup(`
          <div class="court-map-popup">
            <strong>${this.courtName}</strong>
            ${this.address ? `<p class="small text-muted mb-1">${this.address}</p>` : ''}
            <a href="${this.googleMapsUrl}" target="_blank" class="btn btn-sm btn-success text-white py-0 px-2 rounded-pill">เปิดนำทาง</a>
          </div>
        `);
      }
    }
  }

  getCurrentLocation(): void {
    if (!navigator.geolocation) {
      alert('เบราว์เซอร์ของคุณไม่รองรับการค้นหาพิกัด GPS');
      return;
    }

    this.isLocating.set(true);

    navigator.geolocation.getCurrentPosition(
      (position) => {
        this.isLocating.set(false);
        const lat = Number(position.coords.latitude.toFixed(6));
        const lng = Number(position.coords.longitude.toFixed(6));
        this.currentLat.set(lat);
        this.currentLng.set(lng);

        if (this.leafletMap) {
          this.leafletMap.flyTo([lat, lng], 17, { duration: 1.2 });
        }

        if (this.mode === 'view' && this.viewMarker) {
          this.viewMarker.setLatLng([lat, lng]);
        }

        this.emitCoordinates(lat, lng);
      },
      (error) => {
        this.isLocating.set(false);
        console.warn('Geolocation error:', error);
        alert('ไม่สามารถระบุตำแหน่งพิกัดได้ กรุณาอนุญาตการเข้าถึง Location หรือเลื่อนแผนที่ปักหมุดเอง');
      },
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
  }

  toggleMapExpand(): void {
    this.isMapExpanded.update(v => !v);
    setTimeout(() => {
      if (this.leafletMap) {
        this.leafletMap.invalidateSize();
      }
    }, 280);
  }

  openGoogleMaps(): void {
    window.open(this.googleMapsUrl, '_blank');
  }

  openAppleMaps(): void {
    window.open(this.appleMapsUrl, '_blank');
  }

  private emitCoordinates(lat: number, lng: number): void {
    this.coordinatesChange.emit({
      lat,
      lng,
      googleMapsUrl: this.googleMapsUrl
    });
  }
}
