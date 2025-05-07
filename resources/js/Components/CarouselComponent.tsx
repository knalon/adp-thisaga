import React from 'react';
import { Carousel } from '@/Components/ui/carousel';

interface CarouselComponentProps {
  images: string[];
  alt?: string;
}

export default function CarouselComponent({ images, alt = 'Car image' }: CarouselComponentProps) {
  return (
    <Carousel>
      {images.map((image, index) => (
        <img
          key={index}
          src={image}
          alt={`${alt} ${index + 1}`}
          className="w-full h-96 object-cover"
        />
      ))}
    </Carousel>
  );
}
