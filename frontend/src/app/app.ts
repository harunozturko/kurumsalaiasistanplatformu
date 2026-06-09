import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

/**
 * Kök bileşen. Tüm sayfalar router-outlet içine çizilir.
 */
@Component({
  selector: 'app-root',
  imports: [RouterOutlet],
  templateUrl: './app.html',
  styleUrl: './app.scss',
})
export class App {}
